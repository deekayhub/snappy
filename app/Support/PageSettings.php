<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PageSettings
{
    public static function all(): array
    {
        $settings = self::defaults();

        if (! Storage::disk('local')->exists('page-settings.json')) {
            return $settings;
        }

        $stored = json_decode(
            Storage::disk('local')->get('page-settings.json'),
            true
        );

        if (! is_array($stored)) {
            return $settings;
        }

        return array_replace_recursive($settings, $stored);
    }

    public static function save(array $settings): void
    {
        Storage::disk('local')->put(
            'page-settings.json',
            json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    public static function defaults(): array
    {
        return [
            'faq_page' => [
                'banner_title' => 'FAQ',
                'banner_description' => 'Answers to the most common questions about SnappyQuote.',
            ],
            'faq_section' => [
                'title' => 'Frequently Asked Questions',
                'highlight_text' => '(FAQs)',
                'description' => 'Find answers to the most common questions about posting jobs, receiving quotes and joining as a supplier.',
                'cta_title' => 'Still have questions?',
                'cta_description' => 'Can’t find the answer you’re looking for? Our friendly team is here to help reach out anytime.',
                'cta_button_text' => 'Get in touch',
                'cta_button_link' => '/contact-us',
                'items' => [
                    [
                        'question' => 'How does the platform work?',
                        'answer' => 'When a customer posts a job, verified suppliers receive the request based on their categories. Suppliers then send quotes, and the customer chooses the best option.',
                    ],
                    [
                        'question' => 'Is it free for customers to use?',
                        'answer' => 'Yes. Customers can post their requirements and compare quotes without paying to access the platform.',
                    ],
                    [
                        'question' => 'How do suppliers receive leads?',
                        'answer' => 'Suppliers receive relevant job opportunities based on the organisation categories they selected in their profile.',
                    ],
                    [
                        'question' => 'Can I compare multiple quotes?',
                        'answer' => 'Yes. You can review multiple supplier responses and choose the option that best matches your needs and budget.',
                    ],
                ],
            ],
            'home_category_section' => [
                'title' => 'What do you need a quote for?',
                'highlight_text' => 'quote',
                'description' => 'Quickly connect with suppliers who specialise in your exact requirement.',
                'items' => [
                    [
                        'title' => 'Sportswear',
                        'image' => 'assets/images/Sportswear-1.png',
                    ],
                    [
                        'title' => 'Sports Equipment',
                        'image' => 'assets/images/sports-and-equipment.png',
                    ],
                    [
                        'title' => 'Trophies & Awards',
                        'image' => 'assets/images/trophies-awards.png',
                    ],
                    [
                        'title' => 'Signage',
                        'image' => 'assets/images/signage.png',
                    ],
                    [
                        'title' => 'Gifts & Promotional Items',
                        'image' => 'assets/images/gifts-promotions.png',
                    ],
                    [
                        'title' => 'School Uniforms & Supplies',
                        'image' => 'assets/images/uniforms-supplies.png',
                    ],
                ],
            ],
        ];
    }

    public static function imageUrl(?string $path): string
    {
        if (! $path) {
            return asset('assets/images/placeholder.jpg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset($path);
    }
}
