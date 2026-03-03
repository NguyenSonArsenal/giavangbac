<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả category IDs
        $categoryIds = DB::table('category')->pluck('id')->toArray();
        
        if (empty($categoryIds)) {
            $this->command->error('Không có category nào! Vui lòng chạy CategorySeeder trước.');
            return;
        }

        $this->command->info('Bắt đầu tạo 5000 sản phẩm...');

        // Mảng tên sản phẩm mẫu
        $productPrefixes = [
            'Viên uống', 'Thuốc', 'Gel', 'Kem', 'Xịt', 'Dung dịch', 'Cao', 'Dầu',
            'Siro', 'Viên nang', 'Viên sủi', 'Bột', 'Thực phẩm chức năng', 'Vitamin',
            'Kẹo ngậm', 'Miếng dán', 'Băng', 'Gạc', 'Máy đo', 'Kit test', 'Que thử'
        ];

        $productTypes = [
            'giảm đau', 'hạ sốt', 'cảm cúm', 'ho', 'tiêu hóa', 'gan mật', 'tim mạch',
            'huyết áp', 'đường huyết', 'xương khớp', 'bổ não', 'bổ mắt', 'bổ phổi',
            'tăng cường miễn dịch', 'vitamin tổng hợp', 'canxi', 'sắt', 'kẽm',
            'da liễu', 'dị ứng', 'kháng sinh', 'kháng nấm', 'tiểu đường', 'mỡ máu',
            'táo bón', 'trĩ', 'sinh lý nam', 'sinh lý nữ', 'làm đẹp', 'giảm cân',
            'chống nắng', 'dưỡng da', 'trị mụn', 'dưỡng ẩm', 'sát trùng', 'băng gạc',
            'nhiệt kế', 'SpO2', 'huyết áp', 'đường huyết', 'thai', 'rụng trứng',
            'omega 3', 'DHA', 'probiotics', 'collagen', 'coenzyme Q10', 'glucosamine'
        ];

        $brands = [
            'An Khang', 'Traphaco', 'DHG Pharma', 'Hasan', 'Pymepharco', 'OPC',
            'Imexpharm', 'Boston', 'Sanofi', 'Abbott', 'Pfizer', 'GSK', 'Bayer',
            'Blackmores', 'Nature Made', 'Centrum', 'Kirkland', 'Swisse', 'Nordic',
            'Vitatree', 'Healthy Care', 'Costar', 'Bio Island', 'Ostelin', 'Elevit'
        ];

        $suffixes = [
            'Plus', 'Forte', 'Extra', 'Premium', 'Gold', 'Advanced', 'Max', 'Ultra',
            '500mg', '1000mg', '100ml', '200ml', '30 viên', '60 viên', '100 viên'
        ];

        $products = [];
        $slugs = [];
        $batchSize = 500;
        $totalProducts = 5000;

        for ($i = 1; $i <= $totalProducts; $i++) {
            // Tạo tên sản phẩm ngẫu nhiên
            $prefix = $productPrefixes[array_rand($productPrefixes)];
            $type = $productTypes[array_rand($productTypes)];
            $brand = $brands[array_rand($brands)];
            $suffix = rand(0, 1) ? ' ' . $suffixes[array_rand($suffixes)] : '';
            
            $name = "{$prefix} {$type} {$brand}{$suffix}";
            
            // Tạo slug unique
            $baseSlug = Str::slug($name);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $slugs)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $slugs[] = $slug;

            // Random category
            $categoryId = $categoryIds[array_rand($categoryIds)];

            // Random price (10,000đ - 5,000,000đ)
            $price = rand(10, 5000) * 1000;

            // Random sale (0%, 5%, 10%, 15%, 20%, 30%, 50%)
            $saleOptions = [0, 0, 0, 5, 10, 15, 20, 30, 50];
            $sale = $saleOptions[array_rand($saleOptions)];

            // Tính giá sale
            $priceSale = $sale > 0 ? round($price * (100 - $sale) / 100, -3) : 0;

            // Random status (90% active, 10% inactive)
            $status = rand(1, 10) <= 9 ? 1 : -1;

            // Random timestamp trong 6 tháng qua
            $createdAt = now()->subDays(rand(0, 180));

            // Tạo content mô tả
            $content = $this->generateProductContent($name, $type);

            $products[] = [
                'name' => $name,
                'slug' => $slug,
                'price' => $price,
                'sale' => $sale,
                'price_sale' => $priceSale,
                'content' => $content,
                'category_id' => $categoryId,
                'image' => 'products/placeholder-' . rand(1, 10) . '.jpg',
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Insert theo batch để tăng performance
            if (count($products) >= $batchSize) {
                DB::table('product')->insert($products);
                $this->command->info("Đã tạo {$i}/{$totalProducts} sản phẩm...");
                $products = [];
            }
        }

        // Insert batch cuối cùng
        if (!empty($products)) {
            DB::table('product')->insert($products);
        }

        $this->command->info('✅ Hoàn thành! Đã tạo 5000 sản phẩm.');
    }

    private function generateProductContent(string $name, string $type): string
    {
        $benefits = [
            'Hỗ trợ điều trị hiệu quả',
            'Giảm triệu chứng nhanh chóng',
            'An toàn cho sức khỏe',
            'Thành phần tự nhiên',
            'Không gây tác dụng phụ',
            'Phù hợp cho mọi lứa tuổi',
            'Được khuyên dùng bởi bác sĩ',
            'Chất lượng cao, giá cả hợp lý',
            'Sản phẩm chính hãng 100%',
            'Cam kết hiệu quả sau 2-4 tuần sử dụng'
        ];

        $usage = [
            'Uống 1-2 viên/lần, ngày 2-3 lần sau ăn',
            'Bôi trực tiếp lên vùng da cần điều trị',
            'Xịt 2-3 lần/ngày vào vùng bị ảnh hưởng',
            'Sử dụng theo chỉ dẫn của bác sĩ',
            'Dùng đều đặn để đạt hiệu quả tốt nhất'
        ];

        $warnings = [
            'Không dùng cho phụ nữ có thai và cho con bú',
            'Tránh xa tầm tay trẻ em',
            'Bảo quản nơi khô ráo, tránh ánh nắng trực tiếp',
            'Đọc kỹ hướng dẫn sử dụng trước khi dùng',
            'Ngưng sử dụng nếu có dấu hiệu dị ứng'
        ];

        $content = "<h3>{$name}</h3>";
        $content .= "<p><strong>Công dụng:</strong></p>";
        $content .= "<ul>";
        for ($i = 0; $i < rand(3, 5); $i++) {
            $content .= "<li>" . $benefits[array_rand($benefits)] . "</li>";
        }
        $content .= "</ul>";
        
        $content .= "<p><strong>Cách dùng:</strong></p>";
        $content .= "<p>" . $usage[array_rand($usage)] . "</p>";
        
        $content .= "<p><strong>Lưu ý:</strong></p>";
        $content .= "<ul>";
        for ($i = 0; $i < rand(2, 3); $i++) {
            $content .= "<li>" . $warnings[array_rand($warnings)] . "</li>";
        }
        $content .= "</ul>";

        return $content;
    }
}
