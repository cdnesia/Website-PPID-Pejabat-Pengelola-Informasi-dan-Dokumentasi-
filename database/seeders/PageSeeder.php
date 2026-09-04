<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'tentang-ppid',
                'title' => 'Tentang PPID',
                'subtitle' => 'Profil Pejabat Pengelola Informasi dan Dokumentasi Universitas Muhammadiyah Jambi.',
                'content' => [
                    'body' => 'Pejabat Pengelola Informasi dan Dokumentasi (PPID) adalah pejabat yang bertanggung jawab di '
                        .'bidang penyimpanan, pendokumentasian, penyediaan, dan/atau pelayanan informasi di Badan Publik, '
                        .'sesuai amanat Undang-Undang No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik. PPID hadir '
                        .'untuk memastikan hak masyarakat memperoleh informasi terpenuhi secara cepat, tepat waktu, '
                        .'biaya ringan, dan cara sederhana.',
                ],
            ],
            [
                'slug' => 'visi-misi',
                'title' => 'Visi dan Misi',
                'subtitle' => 'Arah dan komitmen PPID dalam mewujudkan keterbukaan informasi publik.',
                'content' => [
                    'visi' => 'Terwujudnya layanan informasi publik yang transparan, akuntabel, dan terpercaya demi '
                        .'mendukung tata kelola instansi yang baik.',
                    'misi' => [
                        'Menjamin hak masyarakat memperoleh informasi publik sesuai UU No. 14 Tahun 2008.',
                        'Mewujudkan penyelenggaraan layanan informasi yang cepat, tepat, dan sederhana.',
                        'Mengelola dan mendokumentasikan informasi publik secara akurat dan mutakhir.',
                        'Meningkatkan kualitas pelayanan informasi kepada masyarakat secara berkelanjutan.',
                    ],
                ],
            ],
            [
                'slug' => 'tugas-fungsi',
                'title' => 'Tugas Pokok dan Fungsi',
                'subtitle' => 'Tugas dan fungsi PPID berdasarkan jabatan dalam pengelolaan dan pelayanan informasi publik.',
                'content' => [
                    'items' => [
                        [
                            'title' => 'Atasan PPID',
                            'description' => '<ul><li>Menetapkan kebijakan pelayanan informasi publik di lingkungan instansi.</li>'
                                .'<li>Bertanggung jawab atas pelaksanaan keterbukaan informasi publik secara keseluruhan.</li>'
                                .'<li>Menetapkan pertimbangan tertulis atas kebijakan yang bersifat mendasar dan berdampak luas.</li></ul>',
                        ],
                        [
                            'title' => 'PPID Utama',
                            'description' => '<ul><li>Mengoordinasikan seluruh pelayanan informasi publik di tingkat instansi.</li>'
                                .'<li>Menyimpan, mendokumentasikan, dan menyediakan informasi publik yang berada di bawah kewenangannya.</li>'
                                .'<li>Menerima, memproses, dan menjawab permohonan informasi publik dari masyarakat.</li>'
                                .'<li>Melakukan pengujian konsekuensi atas informasi yang dikecualikan.</li></ul>',
                        ],
                        [
                            'title' => 'PPID Pembantu',
                            'description' => '<ul><li>Membantu PPID Utama dalam pengumpulan dan penyediaan informasi dari unit kerja masing-masing.</li>'
                                .'<li>Mengoordinasikan pemberian informasi publik yang melibatkan unit kerjanya.</li>'
                                .'<li>Menyampaikan informasi dan dokumentasi kepada PPID Utama secara berkala.</li></ul>',
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'struktur-organisasi',
                'title' => 'Struktur Organisasi',
                'subtitle' => 'Susunan kelembagaan PPID Universitas Muhammadiyah Jambi.',
                'content' => [
                    'items' => [
                        [
                            'title' => 'Atasan PPID',
                            'description' => 'Pimpinan tertinggi instansi yang bertanggung jawab atas pelaksanaan keterbukaan informasi publik.',
                        ],
                        [
                            'title' => 'PPID Utama',
                            'description' => 'Mengoordinasikan seluruh pelayanan informasi publik di tingkat instansi.',
                        ],
                        [
                            'title' => 'PPID Pembantu',
                            'description' => 'Membantu PPID Utama dalam pengumpulan dan penyediaan informasi dari masing-masing unit kerja.',
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'alur-layanan',
                'title' => 'Alur Layanan',
                'subtitle' => 'Tahapan proses permohonan informasi publik dari awal hingga jawaban diterima.',
                'content' => [],
            ],
            [
                'slug' => 'dasar-hukum',
                'title' => 'Dasar Hukum',
                'subtitle' => 'Landasan peraturan perundang-undangan yang mengatur keterbukaan informasi publik.',
                'content' => [
                    'items' => [
                        ['title' => 'UU No. 14 Tahun 2008', 'description' => 'Keterbukaan Informasi Publik'],
                        ['title' => 'PP No. 61 Tahun 2010', 'description' => 'Pelaksanaan Undang-Undang Keterbukaan Informasi Publik'],
                        ['title' => 'Permendagri No. 35 Tahun 2010', 'description' => 'Pedoman Pengelolaan Pelayanan Informasi dan Dokumentasi'],
                        ['title' => 'Peraturan Komisi Informasi No. 1 Tahun 2010', 'description' => 'Standar Layanan Informasi Publik'],
                        ['title' => 'Peraturan KI No. 1 Tahun 2021', 'description' => 'Standar Layanan Informasi Publik (SLIP) terbaru'],
                    ],
                ],
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
