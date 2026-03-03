<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Bắt đầu tạo 300 tin tức...');

        // Mảng chủ đề tin tức
        $topics = [
            'Sức khỏe tim mạch',
            'Dinh dưỡng và sức khỏe',
            'Phòng chống bệnh tật',
            'Chăm sóc sức khỏe',
            'Y học hiện đại',
            'Thuốc và điều trị',
            'Sức khỏe phụ nữ',
            'Sức khỏe trẻ em',
            'Sức khỏe người cao tuổi',
            'Làm đẹp tự nhiên',
            'Giảm cân khoa học',
            'Tập luyện và thể thao',
            'Ngủ ngon và sức khỏe',
            'Stress và sức khỏe tinh thần',
            'Vitamin và khoáng chất',
            'Thực phẩm chức năng',
            'Bệnh tiểu đường',
            'Huyết áp cao',
            'Xương khớp',
            'Gan mật',
            'Tiêu hóa',
            'Hô hấp',
            'Da liễu',
            'Mắt và thị lực',
            'Răng miệng',
            'Tai mũi họng'
        ];

        $titleTemplates = [
            '10 cách {topic} hiệu quả nhất',
            'Bí quyết {topic} mà bạn nên biết',
            '{topic}: Những điều cần lưu ý',
            'Chuyên gia chia sẻ về {topic}',
            'Hướng dẫn {topic} cho người mới bắt đầu',
            '{topic} - Xu hướng mới trong y học',
            'Những sai lầm thường gặp khi {topic}',
            'Top 5 phương pháp {topic} được khuyên dùng',
            '{topic}: Giải pháp từ thiên nhiên',
            'Nghiên cứu mới về {topic}',
            'Cách phòng tránh {topic} hiệu quả',
            '{topic} - Những điều bác sĩ không nói với bạn',
            'Thực phẩm tốt cho {topic}',
            'Bài tập đơn giản giúp cải thiện {topic}',
            '{topic}: Từ A đến Z',
            'Mẹo hay cho {topic} hàng ngày',
            'Tại sao {topic} lại quan trọng?',
            'Dấu hiệu cảnh báo về {topic}',
            'Cách tự nhiên để cải thiện {topic}',
            '{topic} ở người trẻ tuổi'
        ];

        $paragraphs = [
            'Theo các chuyên gia y tế, việc duy trì lối sống lành mạnh là yếu tố quan trọng nhất để có một sức khỏe tốt. Điều này bao gồm chế độ ăn uống cân bằng, tập thể dục đều đặn và ngủ đủ giấc.',
            'Nghiên cứu gần đây cho thấy rằng việc bổ sung vitamin và khoáng chất đầy đủ có thể giúp cải thiện đáng kể sức khỏe tổng thể. Tuy nhiên, nên tham khảo ý kiến bác sĩ trước khi sử dụng.',
            'Chế độ ăn giàu rau xanh, trái cây và protein nạc được chứng minh là có lợi cho sức khỏe tim mạch. Hạn chế đường, muối và chất béo bão hòa cũng rất quan trọng.',
            'Tập thể dục ít nhất 30 phút mỗi ngày có thể giúp giảm nguy cơ mắc nhiều bệnh mãn tính như tiểu đường, cao huyết áp và bệnh tim mạch.',
            'Stress kéo dài có thể ảnh hưởng tiêu cực đến sức khỏe. Các phương pháp giảm stress như thiền, yoga và hít thở sâu rất được khuyến khích.',
            'Uống đủ nước mỗi ngày (khoảng 2 lít) giúp cơ thể hoạt động tốt hơn, da dẻ khỏe mạnh và hỗ trợ quá trình thải độc.',
            'Ngủ đủ 7-8 tiếng mỗi đêm là điều cần thiết để cơ thể phục hồi và tái tạo năng lượng. Thiếu ngủ có thể dẫn đến nhiều vấn đề sức khỏe.',
            'Khám sức khỏe định kỳ giúp phát hiện sớm các bệnh lý, từ đó có phương án điều trị kịp thời và hiệu quả hơn.',
            'Việc hạn chế rượu bia và không hút thuốc lá là những bước quan trọng để bảo vệ sức khỏe lâu dài.',
            'Duy trì cân nặng hợp lý thông qua chế độ ăn uống và tập luyện phù hợp giúp giảm nguy cơ mắc nhiều bệnh nguy hiểm.',
            'Bổ sung probiotics từ sữa chua, kim chi có thể giúp cải thiện hệ tiêu hóa và tăng cường miễn dịch.',
            'Vitamin D đóng vai trò quan trọng trong việc hấp thụ canxi và duy trì xương chắc khỏe. Phơi nắng 15-20 phút mỗi ngày là cách tự nhiên để bổ sung vitamin D.',
            'Omega-3 từ cá hồi, cá thu giúp giảm viêm, hỗ trợ tim mạch và cải thiện chức năng não bộ.',
            'Chất chống oxi hóa từ trái cây và rau củ giúp bảo vệ tế bào khỏi tổn thương và làm chậm quá trình lão hóa.',
            'Việc giảm tiêu thụ thực phẩm chế biến sẵn và đồ ăn nhanh có thể cải thiện đáng kể sức khỏe tổng thể.'
        ];

        $news = [];
        $slugs = [];
        $totalNews = 300;

        for ($i = 1; $i <= $totalNews; $i++) {
            // Tạo tiêu đề ngẫu nhiên
            $topic = $topics[array_rand($topics)];
            $template = $titleTemplates[array_rand($titleTemplates)];
            $title = str_replace('{topic}', strtolower($topic), $template);

            // Tạo slug unique
            $baseSlug = Str::slug($title);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $slugs)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $slugs[] = $slug;

            // Tạo mô tả ngắn
            $des = $paragraphs[array_rand($paragraphs)];
            if (strlen($des) > 255) {
                $des = substr($des, 0, 252) . '...';
            }

            // Tạo nội dung HTML
            $content = "<h2>{$title}</h2>";
            $content .= "<p class='lead'>{$paragraphs[array_rand($paragraphs)]}</p>";

            // Thêm 3-6 đoạn văn
            $numParagraphs = rand(3, 6);
            for ($j = 0; $j < $numParagraphs; $j++) {
                $content .= "<p>" . $paragraphs[array_rand($paragraphs)] . "</p>";

                // Thỉnh thoảng thêm heading
                if ($j == 1 || $j == 3) {
                    $subheadings = [
                        'Lợi ích chính',
                        'Cách thực hiện',
                        'Lưu ý quan trọng',
                        'Kết luận',
                        'Khuyến nghị từ chuyên gia',
                        'Những điều cần tránh'
                    ];
                    $content .= "<h3>" . $subheadings[array_rand($subheadings)] . "</h3>";
                }
            }

            // Thêm kết luận
            $content .= "<p><strong>Kết luận:</strong> " . $paragraphs[array_rand($paragraphs)] . "</p>";

            // Random timestamp trong 1 năm qua
            $createdAt = now()->subDays(rand(0, 365));

            $news[] = [
                'title' => ucfirst($title),
                'des' => $des,
                'slug' => $slug,
                'content' => $content,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if ($i % 50 == 0) {
                $this->command->info("Đã tạo {$i}/{$totalNews} tin tức...");
            }
        }

        // Insert tất cả tin tức
        DB::table('new')->insert($news);

        $this->command->info('✅ Hoàn thành! Đã tạo 300 tin tức.');
    }
}
