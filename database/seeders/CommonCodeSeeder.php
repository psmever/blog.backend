<?php

namespace Database\Seeders;

use App\Models\CommonCode;
use Illuminate\Database\Seeder;

class CommonCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            [
                'group_key' => 'post.status',
                'code' => 'draft',
                'label' => '임시 저장',
                'description' => '작성 중인 게시글 상태',
                'sort_order' => 10,
            ],
            [
                'group_key' => 'post.status',
                'code' => 'published',
                'label' => '게시됨',
                'description' => '공개된 게시글 상태',
                'sort_order' => 20,
            ],
            [
                'group_key' => 'post.status',
                'code' => 'archived',
                'label' => '보관됨',
                'description' => '노출에서 제외된 게시글 상태',
                'sort_order' => 30,
                'is_active' => false,
            ],
            [
                'group_key' => 'post.category',
                'code' => 'tech',
                'label' => '기술',
                'description' => '기술 관련 게시글',
                'sort_order' => 10,
            ],
            [
                'group_key' => 'post.category',
                'code' => 'life',
                'label' => '라이프',
                'description' => '일상 / 라이프스타일 게시글',
                'sort_order' => 20,
            ],
            [
                'group_key' => 'post.category',
                'code' => 'notice',
                'label' => '공지',
                'description' => '공지사항 / 업데이트',
                'sort_order' => 30,
            ],
            [
                'group_key' => 'client.type',
                'code' => 'CT01X',
                'label' => 'iOS',
                'description' => 'iOS 모바일 클라이언트',
                'sort_order' => 10,
            ],
            [
                'group_key' => 'client.type',
                'code' => 'CT02Y',
                'label' => 'Android',
                'description' => 'Android 모바일 클라이언트',
                'sort_order' => 20,
            ],
            [
                'group_key' => 'client.type',
                'code' => 'CT03Z',
                'label' => 'Web',
                'description' => '웹 브라우저 클라이언트',
                'sort_order' => 30,
            ],
            [
                'group_key' => 'client.type',
                'code' => 'CT04P',
                'label' => 'Postman',
                'description' => 'API 테스트용 Postman 클라이언트',
                'sort_order' => 40,
                'is_active' => true,
            ],
        ];

        $defaults = [
            'description' => null,
            'sort_order' => 0,
            'is_active' => true,
            'meta' => null,
            'deleted_at' => null,
        ];

        $payload = array_map(
            static fn (array $code) => array_merge($defaults, $code),
            $codes
        );

        CommonCode::query()->upsert(
            $payload,
            ['group_key', 'code'],
            ['label', 'description', 'sort_order', 'is_active', 'meta', 'deleted_at']
        );
    }
}
