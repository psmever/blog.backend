<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use App\Services\PostImageService;
use App\Services\PostService;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class SeedTestPosts extends Command
{
    protected $signature = 'posts:seed-test
        {--count=50 : Number of test posts to create}
        {--user-email=test@example.com : Email address of the test post owner}
        {--no-images : Create posts without local dummy images}
        {--status=published : Post status to create: published or draft}';

    protected $description = 'Seed Korean test posts with optional local dummy images in the local environment';

    /**
     * @var array<int, string>
     */
    private const TOPICS = [
        '로컬 개발 환경에서 API 흐름을 점검하기 위한 테스트 기록',
        '라라벨 백엔드 응답 구조를 확인하기 위한 긴 본문 샘플',
        '블로그 공개 목록 화면의 스크롤 상태를 검증하는 더미 게시글',
        '이미지 포함 마크다운 렌더링을 살펴보기 위한 작성 노트',
        '태그와 발행 일자를 함께 확인하는 통합 테스트 자료',
        '프론트엔드 카드 레이아웃을 채우기 위한 한국어 콘텐츠',
        '검색과 상세 화면 이동을 반복해서 확인하는 개발 메모',
        '게시글 커버 이미지 선택 규칙을 검증하는 긴 테스트 문서',
    ];

    /**
     * @var array<int, string>
     */
    private const TAGS = [
        '라라벨',
        '테스트',
        '블로그',
        '개발노트',
        '마크다운',
        'API',
    ];

    public function __construct(
        private readonly PostService $posts,
        private readonly PostImageService $postImages
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! app()->isLocal()) {
            $this->error('This command can only be run in the local environment.');

            return self::FAILURE;
        }

        $count = $this->countOption();
        if ($count === null) {
            $this->error('The --count option must be a positive integer.');

            return self::FAILURE;
        }

        $status = (string) $this->option('status');

        if (! in_array($status, [Post::STATUS_DRAFT, Post::STATUS_PUBLISHED], true)) {
            $this->error('The --status option must be either draft or published.');

            return self::FAILURE;
        }

        $email = trim((string) $this->option('user-email'));
        if ($email === '') {
            $this->error('The --user-email option is required.');

            return self::FAILURE;
        }

        $this->callSilent('db:seed', ['--class' => CommonCodeSeeder::class]);

        $user = $this->resolveUser($email);
        $withImages = ! (bool) $this->option('no-images');
        $runKey = now()->format('YmdHis').'-'.Str::lower(Str::random(6));

        $this->withProgressBar(range(1, $count), function (int $number) use ($user, $status, $withImages, $runKey): void {
            $this->createPost($user, $number, $status, $withImages, $runKey);
        });

        $this->newLine();
        $this->info(sprintf(
            'Seeded %d Korean test posts for %s as %s%s.',
            $count,
            $user->email,
            $status,
            $withImages ? ' with local images' : ''
        ));

        return self::SUCCESS;
    }

    private function countOption(): ?int
    {
        $count = filter_var($this->option('count'), FILTER_VALIDATE_INT);

        if (! is_int($count) || $count < 1) {
            return null;
        }

        return $count;
    }

    private function resolveUser(string $email): User
    {
        $user = User::query()->firstOrNew(['email' => $email]);
        $user->name = $user->name ?: '테스트 작성자';
        $user->email_verified_at = $user->email_verified_at ?: now();

        if (! $user->exists) {
            $user->password = Hash::make('password');
            $user->remember_token = Str::random(10);
        }

        $user->save();

        return $user;
    }

    private function createPost(User $user, int $number, string $status, bool $withImages, string $runKey): void
    {
        $postUuid = $this->posts->issueUuid();
        $imageUrl = null;

        if ($withImages) {
            $uploadedImage = $this->dummyImage($number);

            try {
                $image = $this->postImages->uploadForPost(
                    $user,
                    $postUuid,
                    $uploadedImage
                );
            } finally {
                @unlink($uploadedImage->getPathname());
            }

            $imageUrl = $image ? $this->postImages->urlForImage($image) : null;
        }

        $post = $this->posts->saveByUuid($user, $postUuid, [
            'title' => $this->title($number, $runKey),
            'tags' => $this->tagsFor($number),
            'body' => $this->body($number, $imageUrl),
        ]);

        if ($post && $status === Post::STATUS_PUBLISHED) {
            $this->posts->publishByUuid($user, $postUuid);
        }
    }

    private function title(int $number, string $runKey): string
    {
        $topic = self::TOPICS[($number - 1) % count(self::TOPICS)];

        return sprintf('%s %s-%03d', $topic, $runKey, $number);
    }

    /**
     * @return array<int, string>
     */
    private function tagsFor(int $number): array
    {
        return [
            self::TAGS[($number - 1) % count(self::TAGS)],
            self::TAGS[$number % count(self::TAGS)],
            '시드데이터',
        ];
    }

    private function body(int $number, ?string $imageUrl): string
    {
        $topic = self::TOPICS[($number - 1) % count(self::TOPICS)];
        $paragraphs = [
            '# '.$topic,
            '이 글은 로컬 개발 환경에서 목록, 상세, 커버 이미지, 태그 표시를 한 번에 확인하기 위해 만들어진 한국어 테스트 게시글입니다. 실제 운영 콘텐츠는 아니지만 화면의 줄바꿈과 문단 간격, 긴 제목이 들어왔을 때의 표시 방식을 살펴볼 수 있도록 충분한 길이로 작성되었습니다.',
            '첫 번째 문단에서는 게시글의 전반적인 맥락을 설명합니다. 개발 중에는 데이터가 너무 짧거나 서로 비슷하면 UI의 어긋남을 발견하기 어렵기 때문에, 이 샘플은 문장이 조금 길고 자연스럽게 이어지도록 구성했습니다.',
        ];

        if ($imageUrl !== null && $imageUrl !== '') {
            $paragraphs[] = sprintf('![테스트 커버 이미지 %03d](%s)', $number, $imageUrl);
        }

        $paragraphs[] = '이미지 아래에는 본문이 계속 이어집니다. 카드 목록에서는 커버 이미지가 먼저 보이고, 상세 화면에서는 이미지가 마크다운 본문 중간에 포함되어 렌더링되는지 확인할 수 있습니다. 같은 구조의 글이 여러 개 있을 때 페이지네이션이나 커서 기반 조회가 안정적으로 동작하는지도 함께 살펴볼 수 있습니다.';
        $paragraphs[] = '두 번째 설명 문단은 검색, 정렬, 공개 상태 필터 같은 기능을 테스트하기 위한 여분의 텍스트입니다. 문장마다 조금씩 다른 표현을 섞어 두면 화면에서 반복되는 더미 텍스트처럼 보이는 느낌을 줄이고, 실제 블로그 글에 가까운 길이와 밀도를 확인하는 데 도움이 됩니다.';
        $paragraphs[] = sprintf('마지막으로 이 게시글은 %03d번째 테스트 데이터입니다. 제목과 슬러그가 서로 충돌하지 않도록 실행 시각과 번호를 함께 사용하며, 필요할 때 같은 명령을 다시 실행해도 새 샘플 묶음을 추가로 만들 수 있습니다.', $number);

        return implode("\n\n", $paragraphs);
    }

    private function dummyImage(int $number): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'post-seed-image-');
        if ($path === false) {
            throw new RuntimeException('Failed to create a temporary image file.');
        }

        $this->writeCoverImage($path, $number);

        return new UploadedFile(
            $path,
            sprintf('seed-cover-%03d.%s', $number, $this->coverImageExtension()),
            $this->coverImageMimeType(),
            null,
            true
        );
    }

    private function writeCoverImage(string $path, int $number): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            $this->writeSvgCoverImage($path, $number);

            return;
        }

        $image = imagecreatetruecolor(1200, 630);
        if ($image === false) {
            throw new RuntimeException('Failed to create a dummy cover image.');
        }

        $palettes = [
            [[37, 99, 235], [14, 165, 233], [240, 249, 255]],
            [[5, 150, 105], [45, 212, 191], [236, 253, 245]],
            [[124, 58, 237], [217, 70, 239], [250, 245, 255]],
            [[220, 38, 38], [249, 115, 22], [255, 247, 237]],
            [[15, 23, 42], [71, 85, 105], [248, 250, 252]],
        ];
        [$start, $end, $text] = $palettes[($number - 1) % count($palettes)];

        for ($y = 0; $y < 630; $y++) {
            $ratio = $y / 629;
            $color = imagecolorallocate(
                $image,
                (int) round($start[0] + (($end[0] - $start[0]) * $ratio)),
                (int) round($start[1] + (($end[1] - $start[1]) * $ratio)),
                (int) round($start[2] + (($end[2] - $start[2]) * $ratio))
            );
            imageline($image, 0, $y, 1200, $y, $color);
        }

        $overlay = imagecolorallocatealpha($image, 255, 255, 255, 105);
        $shadow = imagecolorallocatealpha($image, 0, 0, 0, 95);
        $textColor = imagecolorallocate($image, $text[0], $text[1], $text[2]);

        imagefilledellipse($image, 1000, 90, 360, 360, $overlay);
        imagefilledellipse($image, 180, 520, 420, 220, $overlay);
        imagefilledrectangle($image, 76, 82, 1124, 548, $shadow);

        imagestring($image, 5, 110, 130, 'BLOG TEST COVER', $textColor);
        imagestring($image, 5, 110, 205, sprintf('POST #%03d', $number), $textColor);
        imagestring($image, 4, 110, 300, 'Korean seed post with local image', $textColor);
        imagestring($image, 3, 110, 360, now()->format('Y-m-d H:i:s'), $textColor);

        if (! imagepng($image, $path)) {
            imagedestroy($image);

            throw new RuntimeException('Failed to write a dummy cover image.');
        }

        imagedestroy($image);
    }

    private function writeSvgCoverImage(string $path, int $number): void
    {
        $palettes = [
            ['#2563eb', '#0ea5e9', '#f0f9ff'],
            ['#059669', '#2dd4bf', '#ecfdf5'],
            ['#7c3aed', '#d946ef', '#faf5ff'],
            ['#dc2626', '#f97316', '#fff7ed'],
            ['#0f172a', '#475569', '#f8fafc'],
        ];
        [$start, $end, $text] = $palettes[($number - 1) % count($palettes)];

        $svg = sprintf(
            <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%%" stop-color="%s"/>
      <stop offset="100%%" stop-color="%s"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#bg)"/>
  <circle cx="1000" cy="90" r="180" fill="#ffffff" opacity="0.28"/>
  <circle cx="180" cy="520" r="210" fill="#ffffff" opacity="0.24"/>
  <rect x="76" y="82" width="1048" height="466" rx="34" fill="#000000" opacity="0.26"/>
  <text x="110" y="160" fill="%s" font-family="Arial, sans-serif" font-size="42" font-weight="700">BLOG TEST COVER</text>
  <text x="110" y="260" fill="%s" font-family="Arial, sans-serif" font-size="76" font-weight="700">POST #%03d</text>
  <text x="110" y="340" fill="%s" font-family="Arial, sans-serif" font-size="34" font-weight="600">Korean seed post with local image</text>
  <text x="110" y="405" fill="%s" font-family="Arial, sans-serif" font-size="28">%s</text>
</svg>
SVG,
            $start,
            $end,
            $text,
            $text,
            $number,
            $text,
            $text,
            now()->format('Y-m-d H:i:s')
        );

        if (file_put_contents($path, $svg) === false) {
            throw new RuntimeException('Failed to write a dummy SVG cover image.');
        }
    }

    private function coverImageExtension(): string
    {
        return function_exists('imagecreatetruecolor') ? 'png' : 'svg';
    }

    private function coverImageMimeType(): string
    {
        return function_exists('imagecreatetruecolor') ? 'image/png' : 'image/svg+xml';
    }
}
