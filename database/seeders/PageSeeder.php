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
                'subtitle' => 'Tanggung jawab utama PPID dalam pengelolaan dan pelayanan informasi publik.',
                'content' => [
                    'items' => [
                        [
                            'title' => 'Penyimpanan dan Pendokumentasian',
                            'description' => 'Menyimpan, mendokumentasikan, dan menyediakan seluruh informasi publik yang berada di bawah kewenangan instansi.',
                        ],
                        [
                            'title' => 'Pelayanan Informasi',
                            'description' => 'Menerima, memproses, dan menjawab permohonan informasi publik yang diajukan oleh masyarakat.',
                        ],
                        [
                            'title' => 'Pengujian Konsekuensi',
                            'description' => 'Melakukan pengujian konsekuensi atas informasi yang dikecualikan sebelum menyatakan suatu informasi tidak dapat diakses publik.',
                        ],
                        [
                            'title' => 'Koordinasi Antar Unit',
                            'description' => 'Mengoordinasikan pemberian informasi publik yang melibatkan unit kerja lain di lingkungan instansi.',
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
