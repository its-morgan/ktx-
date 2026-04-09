<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Landing Page KTX</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="linear-shell transition-colors duration-300">
<div class="linear-shell min-h-screen">
    <header class="linear-navbar-glass sticky top-0 z-50 border-b">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="#hero" class="font-display text-base font-semibold text-slate-900">KTX Đại Học ABC</a>

            <nav class="hidden items-center gap-6 text-sm text-slate-600 md:flex">
                <a href="#gioi-thieu" class="transition hover:text-slate-900">Giới thiệu</a>
                <a href="#phong" class="transition hover:text-slate-900">Phòng ở</a>
                <a href="#gia" class="transition hover:text-slate-900">Bảng giá</a>
                <a href="#quy-trinh" class="transition hover:text-slate-900">Đăng ký</a>
                <a href="#tin-tuc" class="transition hover:text-slate-900">Tin tức</a>
                <a href="#lien-he" class="transition hover:text-slate-900">Liên hệ</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dieuhuong') }}" class="linear-btn-primary">Vào hệ thống</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="linear-btn-secondary">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="linear-btn-secondary">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="linear-btn-primary">Đăng ký ngay</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl space-y-8 px-4 py-6 sm:px-6 lg:px-8">
        <section id="hero" class="linear-panel p-5 sm:p-8">
            <div class="grid gap-6 lg:grid-cols-10 lg:items-center">
                <div class="space-y-4 lg:col-span-6">
                    <div class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Section 1 - Hero (above the fold)</div>
                    <p class="text-xs text-slate-500">Ký túc xá Đại học ABC - Cơ sở 1</p>
                    <h1 class="font-display text-3xl font-semibold leading-tight text-slate-900 sm:text-4xl">Không gian sống lý tưởng cho sinh viên</h1>
                    <p class="max-w-2xl text-sm text-slate-600">Phòng ở tiện nghi, an ninh 24/7, gần trường. Giá từ 800.000đ/tháng, phù hợp cho sinh viên ở nội trú dài hạn theo học kỳ hoặc năm học.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ route('dieuhuong') }}" class="linear-btn-primary">Đi đến trang của tôi</a>
                        @else
                            <a href="{{ route('register') }}" class="linear-btn-primary">Đăng ký ở ngay</a>
                            <a href="{{ route('login') }}" class="linear-btn-secondary">Đăng nhập</a>
                        @endauth
                        <a href="#phong" class="linear-btn-secondary">Xem phòng trống</a>
                    </div>
                    <p class="text-xs italic text-slate-500">Chỉ cần 100m đến trường, ảnh minh họa môi trường sống thực tế.</p>
                </div>

                <div class="space-y-4 lg:col-span-4">
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                        <div class="aspect-video">
                            <video class="h-full w-full object-cover" controls poster="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=80">
                                <source src="" type="video/mp4">
                            </video>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80" alt="Ngoại cảnh KTX" class="h-20 w-full rounded-lg object-cover">
                        <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=600&q=80" alt="Phòng ở" class="h-20 w-full rounded-lg object-cover">
                        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=600&q=80" alt="Không gian chung" class="h-20 w-full rounded-lg object-cover">
                    </div>
                </div>
            </div>
        </section>

        <section id="gioi-thieu" class="linear-panel space-y-4 p-5 sm:p-8">
            <div class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Section 2 - Số liệu nổi bật</div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="linear-card text-center">
                    <div class="text-3xl font-bold text-slate-900">1.200</div>
                    <div class="text-xs text-slate-500">Sinh viên đang ở</div>
                </div>
                <div class="linear-card text-center">
                    <div class="text-3xl font-bold text-slate-900">8</div>
                    <div class="text-xs text-slate-500">Tòa nhà</div>
                </div>
                <div class="linear-card text-center">
                    <div class="text-3xl font-bold text-slate-900">98%</div>
                    <div class="text-xs text-slate-500">Hài lòng</div>
                </div>
                <div class="linear-card text-center">
                    <div class="text-3xl font-bold text-slate-900">24/7</div>
                    <div class="text-xs text-slate-500">Bảo vệ</div>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="linear-panel-muted p-4">
                    <h2 class="font-display text-lg font-semibold text-slate-900">Bản đồ vị trí</h2>
                    <p class="mt-1 text-sm text-slate-600">23 Nguyễn Văn Linh, Quận 7, TP.HCM</p>
                    <div class="mt-3 overflow-hidden rounded-lg border border-slate-200">
                        <iframe title="Bản đồ KTX" src="https://www.openstreetmap.org/export/embed.html?bbox=106.7000%2C10.7200%2C106.7100%2C10.7300&amp;layer=mapnik" class="h-64 w-full border-0"></iframe>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs text-slate-600">
                        <div class="rounded-lg border border-slate-200 bg-white p-2">100m tới trường</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-2">5 phút tới trạm xe buýt</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-2">10 phút tới bệnh viện</div>
                    </div>
                </div>

                <div class="linear-panel-muted p-4">
                    <h2 class="font-display text-lg font-semibold text-slate-900">Tiện ích nội khu</h2>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm sm:grid-cols-3">
                        <div class="rounded-lg border border-slate-200 bg-white p-3">WiFi tốc độ cao</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Bãi xe rộng</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Giặt sấy tự động</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Căng tin</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Phòng gym</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Phòng tự học</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Khu sinh hoạt chung</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Máy bán hàng</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">Camera an ninh</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="phong" class="linear-panel space-y-4 p-5 sm:p-8">
            <div class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Section 3 - Danh mục phòng</div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article class="linear-card p-3">
                    <img src="https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80" alt="Phòng đơn" class="h-32 w-full rounded-lg object-cover">
                    <h3 class="mt-3 font-semibold text-slate-900">Phòng đơn</h3>
                    <p class="text-sm text-slate-600">2.500.000đ/tháng</p>
                    <span class="mt-2 inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-700">Còn 3 phòng</span>
                </article>
                <article class="linear-card p-3">
                    <img src="https://images.unsplash.com/photo-1560448204-603b3fc33ddc?auto=format&fit=crop&w=900&q=80" alt="Phòng đôi" class="h-32 w-full rounded-lg object-cover">
                    <h3 class="mt-3 font-semibold text-slate-900">Phòng đôi</h3>
                    <p class="text-sm text-slate-600">1.500.000đ/tháng</p>
                    <span class="mt-2 inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-700">Còn 8 phòng</span>
                </article>
                <article class="linear-card p-3">
                    <img src="https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=900&q=80" alt="Phòng 4 người" class="h-32 w-full rounded-lg object-cover">
                    <h3 class="mt-3 font-semibold text-slate-900">Phòng 4 người</h3>
                    <p class="text-sm text-slate-600">900.000đ/tháng</p>
                    <span class="mt-2 inline-flex rounded-full bg-amber-100 px-2 py-1 text-xs text-amber-700">Sắp có</span>
                </article>
                <article class="linear-card p-3">
                    <img src="https://images.unsplash.com/photo-1493666438817-866a91353ca9?auto=format&fit=crop&w=900&q=80" alt="Phòng 6 người" class="h-32 w-full rounded-lg object-cover">
                    <h3 class="mt-3 font-semibold text-slate-900">Phòng 6 người</h3>
                    <p class="text-sm text-slate-600">800.000đ/tháng</p>
                    <span class="mt-2 inline-flex rounded-full bg-rose-100 px-2 py-1 text-xs text-rose-700">Hết chỗ</span>
                </article>
            </div>

            <div id="gia" class="grid gap-4 lg:grid-cols-2">
                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">Bảng giá theo kỳ</h3>
                    <div class="mt-3 overflow-x-auto">
                        <table class="min-w-full text-left text-sm text-slate-600">
                            <thead class="text-xs uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="py-2 pr-4">Loại phòng</th>
                                    <th class="py-2 pr-4">Theo tháng</th>
                                    <th class="py-2 pr-4">Theo học kỳ</th>
                                    <th class="py-2">Theo năm</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">Phòng đơn</td><td class="py-2 pr-4">2.500.000đ</td><td class="py-2 pr-4">11.500.000đ</td><td class="py-2">22.000.000đ</td></tr>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">Phòng đôi</td><td class="py-2 pr-4">1.500.000đ</td><td class="py-2 pr-4">6.900.000đ</td><td class="py-2">13.200.000đ</td></tr>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">Phòng 4 người</td><td class="py-2 pr-4">900.000đ</td><td class="py-2 pr-4">4.200.000đ</td><td class="py-2">8.100.000đ</td></tr>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">Phòng 6 người</td><td class="py-2 pr-4">800.000đ</td><td class="py-2 pr-4">3.700.000đ</td><td class="py-2">7.200.000đ</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">So sánh loại phòng</h3>
                    <div class="mt-3 overflow-x-auto">
                        <table class="min-w-full text-left text-sm text-slate-600">
                            <thead class="text-xs uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="py-2 pr-4">Tiêu chí</th>
                                    <th class="py-2 pr-4">Đơn</th>
                                    <th class="py-2 pr-4">Đôi</th>
                                    <th class="py-2 pr-4">4 người</th>
                                    <th class="py-2">6 người</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">Diện tích</td><td>18m²</td><td>24m²</td><td>30m²</td><td>34m²</td></tr>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">WC riêng</td><td>Có</td><td>Có</td><td>Có</td><td>Dùng chung</td></tr>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">Phù hợp</td><td>Cần yên tĩnh</td><td>2 bạn cùng nhóm</td><td>Nhóm nhỏ</td><td>Tiết kiệm chi phí</td></tr>
                                <tr class="border-t border-slate-200"><td class="py-2 pr-4">Tình trạng</td><td class="text-emerald-700">Còn phòng</td><td class="text-emerald-700">Còn phòng</td><td class="text-amber-700">Sắp có</td><td class="text-rose-700">Hết chỗ</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section id="quy-trinh" class="linear-panel space-y-4 p-5 sm:p-8">
            <div class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Section 4 - Quy trình & nội quy</div>

            <div class="linear-panel-muted p-4">
                <h3 class="font-display text-lg font-semibold text-slate-900">Quy trình đăng ký 5 bước</h3>
                <ol class="mt-3 grid gap-3 text-sm text-slate-700 sm:grid-cols-5">
                    <li class="rounded-lg border border-slate-200 bg-white p-3"><span class="font-semibold text-brand-700">1.</span> Tạo tài khoản</li>
                    <li class="rounded-lg border border-slate-200 bg-white p-3"><span class="font-semibold text-brand-700">2.</span> Chọn phòng</li>
                    <li class="rounded-lg border border-slate-200 bg-white p-3"><span class="font-semibold text-brand-700">3.</span> Nộp hồ sơ</li>
                    <li class="rounded-lg border border-slate-200 bg-white p-3"><span class="font-semibold text-brand-700">4.</span> Ký hợp đồng</li>
                    <li class="rounded-lg border border-slate-200 bg-white p-3"><span class="font-semibold text-emerald-700">5.</span> Nhận phòng</li>
                </ol>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">Nội quy ký túc xá</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li>Giờ giới nghiêm: 23:00 mỗi ngày.</li>
                        <li>Giờ khách: 08:00 - 21:00, đăng ký tại quầy bảo vệ.</li>
                        <li>Cấm tự ý thay đổi tài sản, thiết bị trong phòng.</li>
                        <li>Tuân thủ quy định phòng cháy chữa cháy, an ninh chung.</li>
                    </ul>
                </div>

                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">Điều kiện được ở KTX</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li>Ưu tiên sinh viên năm 1, năm 2.</li>
                        <li>Sinh viên ngoại tỉnh hoặc có hoàn cảnh khó khăn.</li>
                        <li>Điểm rèn luyện và GPA đạt mức quy định từng đợt.</li>
                        <li>Không vi phạm kỷ luật cấp trường trong kỳ gần nhất.</li>
                    </ul>
                </div>
            </div>

            <div class="linear-panel-muted p-4">
                <h3 class="font-display text-lg font-semibold text-slate-900">FAQ</h3>
                <div class="mt-3 space-y-2 text-sm text-slate-600">
                    <details class="rounded-lg border border-slate-200 bg-white p-3"><summary class="cursor-pointer font-medium text-slate-900">Làm sao biết phòng còn trống?</summary><p class="mt-2">Xem trạng thái theo thời gian thực ở mục Danh mục phòng hoặc đăng nhập để nhận thông báo.</p></details>
                    <details class="rounded-lg border border-slate-200 bg-white p-3"><summary class="cursor-pointer font-medium text-slate-900">Có thể đổi phòng giữa kỳ không?</summary><p class="mt-2">Có. Bạn gửi yêu cầu đổi phòng trong tài khoản và chờ quản trị phê duyệt.</p></details>
                    <details class="rounded-lg border border-slate-200 bg-white p-3"><summary class="cursor-pointer font-medium text-slate-900">Có hoàn phí nếu trả phòng sớm không?</summary><p class="mt-2">Phí hoàn trả phụ thuộc điều khoản hợp đồng và thời điểm trả phòng.</p></details>
                </div>
            </div>
        </section>

        <section id="tin-tuc" class="linear-panel space-y-4 p-5 sm:p-8">
            <div class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Section 5 - Tin tức & thông báo</div>

            <div class="grid gap-4 lg:grid-cols-3">
                <article class="linear-card">
                    <p class="text-xs text-slate-500">Thông báo tuyển sinh</p>
                    <h3 class="mt-1 font-semibold text-slate-900">Mở đợt đăng ký KTX học kỳ I (2026-2027)</h3>
                    <p class="mt-2 text-sm text-slate-600">Nhận hồ sơ từ 10/08/2026 đến 25/08/2026. Ưu tiên sinh viên năm nhất.</p>
                </article>
                <article class="linear-card">
                    <p class="text-xs text-slate-500">Sự kiện nội trú</p>
                    <h3 class="mt-1 font-semibold text-slate-900">Ngày hội sinh viên nội trú 2026</h3>
                    <p class="mt-2 text-sm text-slate-600">Hoạt động giao lưu thể thao, workshop kỹ năng và văn nghệ cuối tuần.</p>
                </article>
                <article class="linear-card">
                    <p class="text-xs text-slate-500">Bảo trì</p>
                    <h3 class="mt-1 font-semibold text-slate-900">Thông báo bảo trì điện nước tòa B</h3>
                    <p class="mt-2 text-sm text-slate-600">Thời gian: 13:00 - 16:00 ngày 18/08/2026. Vui lòng chủ động sinh hoạt.</p>
                </article>
            </div>
        </section>

        <section id="xac-thuc" class="linear-panel space-y-4 p-5 sm:p-8">
            <div class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Section 6 - Xác thực</div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">Tạo tài khoản bằng mã số sinh viên</h3>
                    <form action="{{ route('register') }}" method="GET" class="mt-3 space-y-3">
                        <div>
                            <label class="mb-1 block text-xs text-slate-500">Mã số sinh viên</label>
                            <input type="text" name="mssv" placeholder="VD: 22123456" class="linear-input">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-slate-500">Email trường</label>
                            <input type="email" name="email" placeholder="ten@st.abc.edu.vn" class="linear-input">
                        </div>
                        <button type="submit" class="linear-btn-primary w-full">Tiếp tục đăng ký</button>
                    </form>
                </div>

                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">Đăng nhập & khôi phục tài khoản</h3>
                    <div class="mt-3 space-y-2 text-sm">
                        <a href="{{ route('login') }}" class="block rounded-lg border border-slate-200 bg-white px-3 py-2 text-slate-700 transition hover:bg-slate-50">Đăng nhập tài khoản KTX</a>
                        <a href="{{ route('password.request') }}" class="block rounded-lg border border-slate-200 bg-white px-3 py-2 text-slate-700 transition hover:bg-slate-50">Quên mật khẩu</a>
                    </div>
                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <p class="text-xs text-slate-500">Đăng nhập bằng tài khoản trường (SSO/OAuth)</p>
                        <button type="button" class="linear-btn-secondary mt-2 w-full">Tiếp tục với SSO trường</button>
                    </div>
                </div>
            </div>
        </section>

        <section id="lien-he" class="linear-panel space-y-4 p-5 sm:p-8">
            <div class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Section 7 - Liên hệ</div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">Ban quản lý ký túc xá</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li>Địa chỉ: 123 Nguyễn Văn Linh, Quận 7, TP.HCM</li>
                        <li>Điện thoại: 028 3456 7890</li>
                        <li>Email: ktx@abc.edu.vn</li>
                        <li>Giờ làm việc: 07:30 - 17:30 (Thứ 2 - Thứ 7)</li>
                    </ul>
                    <div class="mt-4 rounded-lg border border-slate-200 bg-white p-3 text-sm text-slate-600">
                        Live chat cơ bản: biểu tượng chat góc phải để gửi câu hỏi nhanh cho bot hỗ trợ.
                    </div>
                </div>

                <div class="linear-panel-muted p-4">
                    <h3 class="font-display text-lg font-semibold text-slate-900">Gửi câu hỏi</h3>
                    <form action="{{ route('landing.lienhe') }}" method="POST" class="mt-3 space-y-3">
                        @csrf
                        @if (session('lienhe_thanhcong'))
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                                {{ session('lienhe_thanhcong') }}
                            </div>
                        @endif
                        <div>
                            <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" placeholder="Họ và tên" class="linear-input">
                            @error('ho_ten')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email liên hệ" class="linear-input">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <textarea rows="4" name="noi_dung" placeholder="Nội dung câu hỏi" class="linear-textarea">{{ old('noi_dung') }}</textarea>
                            @error('noi_dung')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="linear-btn-primary w-full">Gửi liên hệ</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto grid w-full max-w-7xl gap-6 px-4 py-8 text-sm text-slate-600 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div>
                <h4 class="font-display text-base font-semibold text-slate-900">KTX Đại học ABC</h4>
                <p class="mt-2">Nơi ở an toàn, tiện nghi và kết nối cộng đồng sinh viên.</p>
            </div>
            <div>
                <h4 class="font-display text-base font-semibold text-slate-900">Dịch vụ</h4>
                <ul class="mt-2 space-y-1">
                    <li>Đăng ký phòng</li>
                    <li>Bảng giá</li>
                    <li>Nội quy KTX</li>
                </ul>
            </div>
            <div>
                <h4 class="font-display text-base font-semibold text-slate-900">Hỗ trợ</h4>
                <ul class="mt-2 space-y-1">
                    <li>FAQ</li>
                    <li>Liên hệ BQL</li>
                    <li>Báo sự cố</li>
                </ul>
            </div>
            <div>
                <h4 class="font-display text-base font-semibold text-slate-900">Theo dõi</h4>
                <ul class="mt-2 space-y-1">
                    <li>Facebook</li>
                    <li>Zalo OA</li>
                    <li>YouTube</li>
                </ul>
            </div>
        </div>
    </footer>
</div>

<div class="fixed bottom-5 right-5 z-50">
    <button id="chat-toggle" type="button" class="linear-btn-primary">Chat hỗ trợ</button>
    <div id="chat-box" class="linear-modal-card mt-3 hidden w-80 p-4">
        <div class="text-sm font-semibold text-slate-900">Chatbot KTX</div>
        <p class="mt-1 text-xs text-slate-500">Xin chào, bạn cần hỗ trợ về đăng ký phòng hay bảng giá?</p>
        <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-2 text-xs text-slate-600">Gợi ý: "Còn phòng đôi không?"</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('chat-toggle');
        const box = document.getElementById('chat-box');

        if (toggle && box) {
            toggle.addEventListener('click', function () {
                box.classList.toggle('hidden');
            });
        }
    });
</script>
</body>
</html>
