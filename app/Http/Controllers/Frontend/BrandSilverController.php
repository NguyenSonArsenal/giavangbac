<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SilverPriceHistory;

class BrandSilverController extends Controller
{
    /**
     * Lấy giá mới nhất (KG + LUONG) từ DB để SSR.
     */
    private function latestPrices(string $source, array $units = ['KG', 'LUONG']): array
    {
        $result = [];
        foreach ($units as $unit) {
            $row = SilverPriceHistory::where('source', $source)
                ->where('unit', $unit)
                ->orderByDesc('recorded_at')
                ->first();
            if ($row) {
                $result[$unit] = $row;
            }
        }
        return $result;
    }

    // ── Phú Quý ──────────────────────────────────────────────────────────
    public function phuquy()
    {
        $prices = $this->latestPrices('phuquy');

        $brand = [
            'key'         => 'phuquy',
            'name'        => 'Phú Quý 999',
            'name_short'  => 'Phú Quý',
            'slug'        => 'gia-bac-phu-quy',
            'api'         => 'silver',
            'color'       => '#b0bec5',
            'color2'      => '#78909c',
            'gradient'    => 'linear-gradient(135deg,#b0bec5,#546e7a)',
            'icon'        => '🥈',
            'units'       => ['KG', 'LUONG'],
            'default_unit'=> 'KG',
            'unit_labels' => ['KG' => 'KG', 'LUONG' => 'Lượng'],
            'title'       => 'Giá Bạc Phú Quý 999 Hôm Nay ' . now()->format('d/m/Y') . ' – Mua Vào Bán Ra Mới Nhất',
            'description' => 'Cập nhật giá bạc Phú Quý 999 mua vào bán ra hôm nay ' . now()->format('d/m/Y') . '. Bảng giá bạc Phú Quý theo KG và lượng, cập nhật mỗi 30 phút từ nguồn chính thức.',
            'about'       => 'Phú Quý là một trong những thương hiệu vàng bạc uy tín hàng đầu tại Việt Nam. Bạc Phú Quý 999 (hàm lượng 99.9%) được kinh doanh theo nhiều đơn vị: kilogram (KG) và lượng (1 lượng = 37.5 gram). Đây là lựa chọn phổ biến của các nhà đầu tư và người tiêu dùng nhờ chất lượng đảm bảo, được kiểm định nghiêm ngặt. Giá bạc Phú Quý thường biến động theo thị trường quốc tế (XAG/USD) và tỷ giá USD/VND. GiáVàng.vn cập nhật giá bạc Phú Quý 999 tự động mỗi 30 phút trong giờ thị trường, giúp bạn theo dõi xu hướng chính xác nhất.',
            'faqs'        => [
                ['q' => 'Giá bạc Phú Quý 999 hôm nay là bao nhiêu?',       'a' => 'Giá bạc Phú Quý 999 được cập nhật tự động mỗi 30 phút. Xem bảng giá phía trên để có số liệu mới nhất theo đơn vị KG và lượng.'],
                ['q' => 'Bạc Phú Quý 999 có hàm lượng bạc bao nhiêu?',     'a' => 'Bạc Phú Quý 999 có hàm lượng bạc tinh khiết 99.9%, thuộc loại bạc cao cấp nhất trên thị trường.'],
                ['q' => 'Mua bạc Phú Quý ở đâu?',                          'a' => 'Bạc Phú Quý 999 được bán tại hệ thống cửa hàng Phú Quý trên toàn quốc và website chính thức phuquy.com.vn.'],
                ['q' => 'Giá bạc Phú Quý cập nhật lúc nào?',               'a' => 'Giá bạc Phú Quý trên GiáVàng.vn được tự động cập nhật mỗi 30 phút, lấy từ nguồn chính thức của Phú Quý.'],
            ],
        ];

        return view('frontend.brand.silver', compact('brand', 'prices'));
    }

    // ── Ancarat ──────────────────────────────────────────────────────────
    public function ancarat()
    {
        $prices = $this->latestPrices('ancarat');

        $brand = [
            'key'         => 'ancarat',
            'name'        => 'Bạc 999 – Ancarat',
            'name_short'  => 'Ancarat',
            'slug'        => 'gia-bac-ancarat',
            'api'         => 'ancarat',
            'color'       => '#06b6d4',
            'color2'      => '#0284c7',
            'gradient'    => 'linear-gradient(135deg,#06b6d4,#0284c7)',
            'icon'        => '🏅',
            'units'       => ['LUONG', 'KG'],
            'default_unit'=> 'KG',
            'unit_labels' => ['KG' => 'KG', 'LUONG' => 'Lượng'],
            'title'       => 'Giá Bạc Ancarat 999 Hôm Nay ' . now()->format('d/m/Y') . ' – Mua Vào Bán Ra',
            'description' => 'Cập nhật giá bạc Ancarat 999 mua vào bán ra hôm nay ' . now()->format('d/m/Y') . '. Theo dõi giá bạc Ancarat theo KG và lượng, cập nhật mỗi 30 phút.',
            'about'       => 'Ancarat là thương hiệu bạc uy tín với sản phẩm bạc tinh khiết hàm lượng 999 (99.9%). Bạc Ancarat được phân phối rộng rãi tại Việt Nam, được ưa chuộng bởi giới đầu tư cá nhân và doanh nghiệp. Sản phẩm bạc Ancarat có nhiều dạng: bạc thỏi, bạc miếng theo lượng và theo kilogram, đáp ứng nhu cầu đa dạng từ nhà đầu tư nhỏ lẻ đến các tổ chức. Giá bạc Ancarat biến động theo thị trường bạc quốc tế. GiáVàng.vn tổng hợp và cập nhật giá bạc Ancarat tự động giúp người dùng theo dõi xu hướng giá thuận tiện nhất.',
            'faqs'        => [
                ['q' => 'Giá bạc Ancarat hôm nay là bao nhiêu?',       'a' => 'Giá bạc Ancarat được cập nhật mỗi 30 phút. Xem bảng giá phía trên để có thông tin mua vào và bán ra mới nhất.'],
                ['q' => 'Bạc Ancarat 999 có nghĩa là gì?',              'a' => 'Bạc 999 nghĩa là hàm lượng bạc tinh khiết đạt 99.9%, là loại bạc tiêu chuẩn cao nhất được giao dịch trên thị trường.'],
                ['q' => 'Chênh lệch mua vào bán ra của bạc Ancarat?',  'a' => 'Chênh lệch (spread) giữa giá mua vào và bán ra của Ancarat thường dao động tùy thời điểm, phản ánh phí giao dịch của nhà cung cấp.'],
                ['q' => 'Giá bạc Ancarat cập nhật bao giờ?',           'a' => 'GiáVàng.vn tự động lấy giá bạc Ancarat từ nguồn chính thức và cập nhật mỗi 30 phút.'],
            ],
        ];

        return view('frontend.brand.silver', compact('brand', 'prices'));
    }

    // ── DOJI ─────────────────────────────────────────────────────────────
    public function doji()
    {
        $prices = $this->latestPrices('doji');

        $brand = [
            'key'         => 'doji',
            'name'        => 'Bạc 99.9 – DOJI',
            'name_short'  => 'DOJI',
            'slug'        => 'gia-bac-doji',
            'api'         => 'doji',
            'color'       => '#f87171',
            'color2'      => '#dc2626',
            'gradient'    => 'linear-gradient(135deg,#dc2626,#991b1b)',
            'icon'        => '🔴',
            'units'       => ['LUONG'],
            'default_unit'=> 'LUONG',
            'unit_labels' => ['LUONG' => '1 Lượng'],
            'title'       => 'Giá Bạc DOJI 99.9 Hôm Nay ' . now()->format('d/m/Y') . ' – Chính Thức',
            'description' => 'Giá bạc DOJI 99.9 hôm nay ' . now()->format('d/m/Y') . '. Bảng giá bạc DOJI mua vào bán ra theo lượng, cập nhật mỗi 30 phút từ tập đoàn vàng bạc DOJI.',
            'about'       => 'DOJI là tập đoàn vàng bạc đá quý hàng đầu Việt Nam với hơn 30 năm kinh nghiệm. Bạc DOJI 99.9 (hàm lượng 99.9%) là sản phẩm tiêu chuẩn cao được kiểm định chất lượng nghiêm ngặt theo quy chuẩn quốc tế. Sản phẩm bạc DOJI được bán chủ yếu theo đơn vị lượng (1 lượng = 37.5 gram và 5 lượng). Nhờ thương hiệu mạnh và hệ thống phân phối rộng khắp, bạc DOJI là lựa chọn an toàn và tin cậy cho nhà đầu tư. GiáVàng.vn cập nhật giá bạc DOJI tự động 30 phút/lần từ nguồn chính thức của tập đoàn DOJI.',
            'faqs'        => [
                ['q' => 'Giá bạc DOJI hôm nay là bao nhiêu một lượng?', 'a' => 'Giá bạc DOJI 99.9 mua vào bán ra hôm nay được cập nhật tự động trong bảng phía trên theo đơn vị 1 lượng (37.5 gram).'],
                ['q' => 'Bạc DOJI 99.9 khác gì với bạc 999?',           'a' => 'Bạc DOJI 99.9 nghĩa là hàm lượng bạc tinh 99.9%, tương đương với bạc 999 theo tiêu chuẩn quốc tế.'],
                ['q' => 'Mua bạc DOJI ở đâu?',                          'a' => 'Bạc DOJI được bán tại hệ thống showroom DOJI trên toàn quốc và website chính thức doji.vn.'],
                ['q' => 'Tại sao giá bạc DOJI thay đổi liên tục?',      'a' => 'Giá bạc phụ thuộc vào giá bạc quốc tế (XAG/USD) và tỷ giá USD/VND, vì vậy biến động thường xuyên theo thị trường thế giới.'],
            ],
        ];

        return view('frontend.brand.silver', compact('brand', 'prices'));
    }

    // ── Kim Ngân Phúc ────────────────────────────────────────────────────
    public function kimNganPhuc()
    {
        $prices = $this->latestPrices('kimnganphuc');

        $brand = [
            'key'         => 'kimnganphuc',
            'name'        => 'Bạc 999 Kim Ngân Phúc',
            'name_short'  => 'Kim Ngân Phúc',
            'slug'        => 'gia-bac-kim-ngan-phuc',
            'api'         => 'kimnganphuc',
            'color'       => '#a78bfa',
            'color2'      => '#7c3aed',
            'gradient'    => 'linear-gradient(135deg,#a78bfa,#7c3aed)',
            'icon'        => 'KNP',
            'units'       => ['KG', 'LUONG'],
            'default_unit'=> 'KG',
            'unit_labels' => ['KG' => 'KG', 'LUONG' => 'Lượng'],
            'title'       => 'Giá Bạc Kim Ngân Phúc 999 Hôm Nay ' . now()->format('d/m/Y') . ' – Mới Nhất',
            'description' => 'Cập nhật giá bạc Kim Ngân Phúc 999 hôm nay ' . now()->format('d/m/Y') . '. Xem giá bạc Kim Ngân Phúc mua vào bán ra theo KG và lượng, tự động cập nhật mỗi 30 phút.',
            'about'       => 'Kim Ngân Phúc là đơn vị kinh doanh bạc chuyên nghiệp tại Việt Nam, cung cấp bạc tinh khiết hàm lượng 999 (99.9%) chất lượng cao. Sản phẩm bạc Kim Ngân Phúc được phân loại theo hai đơn vị phổ biến: bạc thỏi 1 kilogram và bạc theo lượng (mỹ nghệ), phù hợp với nhiều đối tượng khách hàng khác nhau. Đây là lựa chọn tốt cho nhà đầu tư muốn tích lũy bạc vật chất dài hạn. GiáVàng.vn thu thập và cập nhật giá bạc Kim Ngân Phúc tự động mỗi 30 phút từ trang chính thức kimnganphuc.vn.',
            'faqs'        => [
                ['q' => 'Giá bạc Kim Ngân Phúc hôm nay là bao nhiêu?',      'a' => 'Giá bạc Kim Ngân Phúc 999 mua vào và bán ra hôm nay được cập nhật trong bảng phía trên theo đơn vị KG và lượng.'],
                ['q' => 'Kim Ngân Phúc bán bạc theo đơn vị nào?',           'a' => 'Kim Ngân Phúc cung cấp bạc 999 theo hai đơn vị chính: bạc thỏi 1 kilogram và bạc miếng mỹ nghệ theo lượng (1 lượng = 37.5g).'],
                ['q' => 'Mua bạc Kim Ngân Phúc ở đâu?',                     'a' => 'Có thể mua bạc Kim Ngân Phúc 999 tại website kimnganphuc.vn hoặc liên hệ trực tiếp với công ty.'],
                ['q' => 'Giá bạc Kim Ngân Phúc và Phú Quý có khác nhau không?', 'a' => 'Giá bạc giữa các thương hiệu thường chênh lệch nhỏ do chính sách và phí giao dịch khác nhau. Xem trang so sánh giá để biết chi tiết.'],
            ],
        ];

        return view('frontend.brand.silver', compact('brand', 'prices'));
    }
}
