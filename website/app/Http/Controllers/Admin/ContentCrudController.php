<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * কনটেন্ট ব্যবস্থাপনার সাধারণ ভিত্তি।
 *
 * সেবা, অভিজ্ঞতা, যোগ্যতা, মতামত, গ্যালারি, নোটিশ, প্রশ্নোত্তর —
 * সাতটি তালিকারই কাজ একই: দেখা, যোগ করা, সম্পাদনা, মুছে ফেলা,
 * ক্রম বদলানো ও সক্রিয়/নিষ্ক্রিয় করা।
 *
 * আলাদা করে সাতটি কন্ট্রোলার ও চোদ্দটি ভিউ লিখলে একই কোড বারবার
 * লিখতে হতো, আর একটিতে ত্রুটি সারালে বাকিগুলোতে থেকে যেত।
 * তাই এখানে একবার লেখা হয়েছে; প্রতিটি তালিকা শুধু নিজের
 * ফিল্ডের তালিকা জানিয়ে দেয়।
 */
abstract class ContentCrudController extends Controller
{
    /** কোন মডেল */
    abstract protected function modelClass(): string;

    /**
     * পাতার পরিচয় ও ফর্মের ঘরগুলো।
     *
     * @return array{title: string, route: string, singular: string, fields: array, hint?: string}
     */
    abstract protected function config(): array;

    public function index(): View
    {
        $cfg = $this->config();

        return view('admin.content.index', [
            'cfg'   => $cfg,
            'items' => $this->modelClass()::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.content.form', [
            'cfg'  => $this->config(),
            'item' => new ($this->modelClass()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cfg = $this->config();
        $data = $this->validated($request, $cfg);

        $data['sort_order'] = $this->modelClass()::nextSortOrder();

        $item = $this->modelClass()::create($data);

        ActivityLog::record('created', $item, "{$cfg['singular']} যোগ করা হয়েছে");

        return redirect()
            ->route("admin.{$cfg['route']}.index")
            ->with('success', "{$cfg['singular']} যোগ করা হয়েছে।");
    }

    public function edit(string|int $id): View
    {
        return view('admin.content.form', [
            'cfg'  => $this->config(),
            'item' => $this->modelClass()::findOrFail($id),
        ]);
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $cfg = $this->config();
        $item = $this->modelClass()::findOrFail($id);

        $item->update($this->validated($request, $cfg, $item));

        ActivityLog::record('updated', $item, "{$cfg['singular']} সম্পাদনা করা হয়েছে");

        return redirect()
            ->route("admin.{$cfg['route']}.index")
            ->with('success', "{$cfg['singular']} সংরক্ষিত হয়েছে।");
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $cfg = $this->config();
        $item = $this->modelClass()::findOrFail($id);

        /* আপলোড করা ছবি ডিস্ক থেকেও মুছে ফেলা — নইলে হোস্টিংয়ের
           জায়গা অকারণে ভরে যেত */
        foreach ($cfg['fields'] as $field) {
            if (($field['type'] ?? '') === 'image' && $item->{$field['name']}) {
                Storage::disk('public')->delete($item->{$field['name']});
            }
        }

        $item->delete();

        ActivityLog::record('deleted', $item, "{$cfg['singular']} মুছে ফেলা হয়েছে");

        return back()->with('success', "{$cfg['singular']} মুছে ফেলা হয়েছে।");
    }

    /*
    |--------------------------------------------------------------------------
    | ভেতরের কাজ
    |--------------------------------------------------------------------------
    */

    protected function validated(Request $request, array $cfg, ?Model $item = null): array
    {
        $rules = [];

        foreach ($cfg['fields'] as $field) {
            $name = $field['name'];
            $required = $field['required'] ?? false;

            /* দ্বিভাষিক ঘর — বাংলা বাধ্যতামূলক, ইংরেজি ঐচ্ছিক।
               ইংরেজি খালি থাকলে ওয়েবসাইটে বাংলাটাই দেখাবে, তাই
               অ্যাডমিনকে সব কনটেন্ট দুবার লিখতে বাধ্য করা হয়নি। */
            if ($field['translatable'] ?? false) {
                $rules["{$name}_bn"] = [$required ? 'required' : 'nullable', 'string', 'max:' . ($field['max'] ?? 255)];
                $rules["{$name}_en"] = ['nullable', 'string', 'max:' . ($field['max'] ?? 255)];

                continue;
            }

            $rules[$name] = match ($field['type'] ?? 'text') {
                'boolean' => ['nullable', 'boolean'],
                'number'  => [$required ? 'required' : 'nullable', 'integer'],
                'image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'url'     => ['nullable', 'url', 'max:255'],
                'select'  => [$required ? 'required' : 'nullable', 'string', 'max:40'],
                'date'    => [$required ? 'required' : 'nullable', 'date'],
                default   => [$required ? 'required' : 'nullable', 'string', 'max:' . ($field['max'] ?? 255)],
            };
        }

        $data = $request->validate($rules);

        foreach ($cfg['fields'] as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'boolean') {
                $data[$name] = $request->boolean($name);
            }

            if ($type === 'image') {
                if ($request->hasFile($name)) {
                    /* পুরনো ছবি মুছে নতুনটি রাখা */
                    if ($item?->{$name}) {
                        Storage::disk('public')->delete($item->{$name});
                    }

                    $data[$name] = $request->file($name)->store($cfg['route'], 'public');
                } else {
                    unset($data[$name]);        // ছবি না দিলে আগেরটাই থাকবে
                }
            }
        }

        return $data;
    }
}
