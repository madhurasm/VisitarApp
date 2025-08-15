<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Content extends Model
{
    protected $guarded = [];

    public static function generate_entity_content($user_id)
    {
        $unique_id = genUniqueStr('contents', 'unique_id', 10, 'UN', false);
        $contents = [
            [
                'user_id' => $user_id,
                'unique_id' => $unique_id,
                'title' => 'Waiver Policy',
                'slug' => 'waiver-policy',
                'lang' => 'en',
                'content' => "English Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user_id,
                'unique_id' => $unique_id,
                'title' => 'Waiver Policy',
                'slug' => 'waiver-policy',
                'lang' => 'es',
                'content' => "Spanish Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($contents as $content) {
            DB::table('contents')->insert($content);
        }
    }

}
