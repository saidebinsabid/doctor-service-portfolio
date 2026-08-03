<?php

namespace App\Models\Concerns;

/**
 * দ্বিভাষিক কনটেন্টের জন্য।
 *
 * মডেলে শুধু লিখতে হয়:
 *     protected array $translatable = ['title', 'description'];
 *
 * তারপর ভিউতে $service->title লিখলেই চলমান ভাষা অনুযায়ী
 * title_bn বা title_en আসবে।
 *
 * ইংরেজি অনুবাদ খালি থাকলে বাংলাটাই দেখানো হয় — অ্যাডমিন সব কনটেন্টের
 * ইংরেজি না লিখলেও সাইট ভাঙবে না, ফাঁকা ঘরও দেখাবে না।
 */
trait HasTranslations
{
    /** নির্দিষ্ট ভাষার মান ফেরত দেয়, না থাকলে বাংলা */
    public function tr(string $field, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        $value = $this->getAttributeValue($field . '_' . $locale);

        if (blank($value)) {
            $value = $this->getAttributeValue($field . '_bn');
        }

        return (string) ($value ?? '');
    }

    /** $model->title দিয়ে অনূদিত মান পাওয়ার সুবিধা */
    public function getAttribute($key)
    {
        if (
            ! array_key_exists($key, $this->attributes)
            && ! $this->hasGetMutator($key)
            && ! $this->hasAttributeMutator($key)
            && ! method_exists($this, $key)
            && in_array($key, $this->translatable ?? [], true)
        ) {
            return $this->tr($key);
        }

        return parent::getAttribute($key);
    }

    /** ভিউতে সব ভাষার মান একসাথে দরকার হলে */
    public function translations(string $field): array
    {
        return [
            'bn' => $this->getAttributeValue($field . '_bn'),
            'en' => $this->getAttributeValue($field . '_en'),
        ];
    }
}
