<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            [
                'name' => 'Hot sale',
                'children' => [
                    'Mỹ phẩm giá sốc',
                    'Khuyến mại hot',
                    'Chăm huyết áp',
                ],
            ],
            [
                'name' => 'Thuốc',
                'children' => [
                    'Cơ xương khớp, gút',
                    'Da liễu, dị ứng',
                    'Dầu, Cao Xoa, Miếng Dán',
                    'Giảm đau, hạ sốt, kháng viêm',
                    'Hô hấp',
                    'Kháng sinh, kháng nấm',
                    'Mắt, tai mũi họng',
                    'Thần kinh, não bộ',
                    'Tiết niệu, sinh dục',
                    'Tiêu hóa, gan mật',
                    'Tim mạch, tiểu đường, mỡ máu',
                    'Thuốc bổ và vitamin',
                    'Thuốc điều trị ung thư, miễn dịch',
                    'Thuốc làm đẹp, giảm cân',
                    'Thuốc giải độc, khử độc – hỗ trợ cai nghiện',
                ],
            ],
            [
                'name' => 'Thực phẩm chức năng',
                'children' => [
                    'Bổ phế, hô hấp',
                    'Hỗ trợ tiêu hóa',
                    'Thảo dược tự nhiên',
                    'Bổ trợ xương khớp',
                    'Tăng sinh lý, bổ thận',
                    'Kẹo ngậm, viên ngậm',
                    'Bổ não',
                    'Bổ gan, thanh nhiệt',
                    'Làm đẹp, giảm cân',
                    'Hỗ trợ trị giãn tĩnh mạch, trĩ, táo bón',
                    'Vitamin và khoáng chất',
                    'Hỗ trợ tiểu đường',
                    'Hỗ trợ tim mạch',
                    'Hỗ trợ trị ung thư',
                    'Dầu cá, bổ mắt',
                ],
            ],
            [
                'name' => 'Thiết bị, dụng cụ y tế',
                'children' => [
                    'Bông gòn, băng gạc, găng tay',
                    'Nước muối, dung dịch sát trùng',
                    'Khẩu trang',
                    'Máy đo huyết áp',
                    'Vớ, đai y khoa',
                    'Kit test Covid',
                    'Máy đo SpO₂',
                    'Que thử thai, rụng trứng',
                    'Mắt kính, tấm chắn giọt bắn',
                    'Các dụng cụ khác',
                    'Nhiệt kế',
                    'Máy đo, que thử đường huyết',
                    'Máy xông khí dung',
                    'Miếng dán, dụng cụ giảm đau, hạ sốt',
                    'Cân sức khoẻ',
                    'Máy massage',
                    'Kim tiêm y tế',
                ],
            ],
            [
                'name' => 'Dược mỹ phẩm',
                'children' => [
                    'Dưỡng da, dưỡng môi',
                    'Tẩy trang',
                    'Mặt nạ',
                    'Kem, xịt chống nắng',
                    'Toner và xịt khoáng',
                    'Dưỡng thể',
                    'Kem, sữa rửa mặt',
                    'Tẩy tế bào chết',
                    'Trị mụn, ngừa sẹo, mờ thâm',
                ],
            ],
            [
                'name' => 'Chăm sóc cá nhân',
                'children' => [
                    'Chăm sóc mắt, tai, mũi, họng',
                    'Chăm sóc răng miệng',
                    'Chăm sóc toàn thân',
                    'Chăm sóc phụ khoa',
                    'Gội, xả, dưỡng tóc',
                    'Bao cao su, gel bôi trơn',
                    'Kem, xịt chống côn trùng',
                    'Thực phẩm, đồ uống',
                    'Dầu, tinh dầu',
                    'Sữa',
                    'Chăm sóc tay, chân',
                ],
            ],
            [
                'name' => 'Chăm sóc trẻ em',
                'children' => [
                    'Khẩu trang cho bé',
                    'Phấn thơm, kem chống hăm',
                    'Đánh răng cho bé',
                    'Dầu gội, sữa tắm cho bé',
                ],
            ],
        ];

        DB::transaction(function () use ($tree) {
            $parentSort = 1;

            foreach ($tree as $parent) {
                $parentId = $this->upsertCategory(
                    null,
                    $parent['name'],
                    $parentSort
                );

                $childSort = 1;
                foreach ($parent['children'] as $childName) {
                    $this->upsertCategory(
                        $parentId,
                        $childName,
                        $childSort
                    );
                    $childSort++;
                }

                $parentSort++;
            }
        });
    }

    private function upsertCategory(?int $parentId, string $name, int $sort): int
    {
        $baseSlug = $this->makeSlug($name);
        $slug = $this->uniqueSlug($baseSlug);

        $existing = DB::table('category')->where('slug', $slug)->first();

        if ($existing) {
            DB::table('category')
                ->where('id', $existing->id)
                ->update([
                    'parent_id'  => $parentId,
                    'name'       => $name,
                    'sort'       => $sort,
                    'updated_at' => now(),
                ]);

            return (int) $existing->id;
        }

        return (int) DB::table('category')->insertGetId([
            'parent_id'  => $parentId,
            'name'       => $name,
            'slug'       => $slug,
            'sort'       => $sort,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeSlug(string $name): string
    {
        $name = str_replace(["–", "—"], "-", $name);

        $slug = Str::slug($name);

        return $slug !== '' ? $slug : ('cat-' . now()->timestamp);
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $i = 2;

        while (DB::table('category')->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
