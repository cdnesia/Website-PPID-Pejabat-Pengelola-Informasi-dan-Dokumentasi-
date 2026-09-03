import { Clock3, FileStack, Lock, Zap } from '@lucide/vue';

export const informationTypes = [
    {
        value: 'berkala',
        label: 'Berkala',
        description: 'Wajib diumumkan secara rutin, seperti profil, program, dan laporan kinerja.',
        icon: FileStack,
        variant: 'default',
        bar: 'bg-primary',
        soft: 'bg-primary/10',
        text: 'text-primary',
        watermark: 'text-primary',
    },
    {
        value: 'serta_merta',
        label: 'Serta-Merta',
        description: 'Wajib diumumkan segera karena berpotensi mengancam hajat hidup orang banyak.',
        icon: Zap,
        variant: 'accent',
        bar: 'bg-accent',
        soft: 'bg-accent/10',
        text: 'text-accent',
        watermark: 'text-accent',
    },
    {
        value: 'setiap_saat',
        label: 'Setiap Saat',
        description: 'Tersedia setiap saat dan diberikan atas permohonan pemohon informasi.',
        icon: Clock3,
        variant: 'secondary',
        bar: 'bg-secondary',
        soft: 'bg-secondary/10',
        text: 'text-amber-700',
        watermark: 'text-secondary',
    },
    {
        value: 'dikecualikan',
        label: 'Dikecualikan',
        description: 'Bersifat rahasia sesuai undang-undang dan tidak dapat diakses publik.',
        icon: Lock,
        variant: 'destructive',
        bar: 'bg-destructive',
        soft: 'bg-destructive/10',
        text: 'text-destructive',
        watermark: 'text-destructive',
    },
];

export const informationTypeMap = Object.fromEntries(informationTypes.map((type) => [type.value, type]));

export function getInformationType(value) {
    return informationTypeMap[value] ?? informationTypes[0];
}
