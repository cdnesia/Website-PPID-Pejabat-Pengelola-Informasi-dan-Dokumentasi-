<script setup>
import { onBeforeUnmount, watch } from 'vue';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import { Bold, Heading2, Heading3, ImagePlus, Italic, Link2, List, ListOrdered, Quote, Redo2, Strikethrough, Undo2 } from '@lucide/vue';
import { cn } from '@/lib/utils';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Tulis isi berita di sini...' },
    class: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Link.configure({ openOnClick: false, autolink: true }),
        Image,
        Placeholder.configure({ placeholder: props.placeholder }),
    ],
    editorProps: {
        attributes: {
            class: 'prose-content min-h-[200px] px-3 py-2 text-sm focus:outline-none',
        },
    },
    onUpdate: ({ editor: instance }) => {
        emit('update:modelValue', instance.getHTML());
    },
});

watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && value !== editor.value.getHTML()) {
            editor.value.commands.setContent(value, false);
        }
    },
);

function setLink() {
    if (!editor.value) return;
    const previousUrl = editor.value.getAttributes('link').href;
    const url = window.prompt('Masukkan URL tautan', previousUrl ?? 'https://');
    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
}

function insertImage() {
    if (!editor.value) return;
    const url = window.prompt('Masukkan URL gambar');
    if (!url) return;
    editor.value.chain().focus().setImage({ src: url }).run();
}

onBeforeUnmount(() => {
    editor.value?.destroy();
});

const buttons = [
    { action: (e) => e.chain().focus().toggleBold().run(), isActive: (e) => e.isActive('bold'), icon: Bold, label: 'Tebal' },
    { action: (e) => e.chain().focus().toggleItalic().run(), isActive: (e) => e.isActive('italic'), icon: Italic, label: 'Miring' },
    { action: (e) => e.chain().focus().toggleStrike().run(), isActive: (e) => e.isActive('strike'), icon: Strikethrough, label: 'Coret' },
    { action: (e) => e.chain().focus().toggleHeading({ level: 2 }).run(), isActive: (e) => e.isActive('heading', { level: 2 }), icon: Heading2, label: 'Judul' },
    { action: (e) => e.chain().focus().toggleHeading({ level: 3 }).run(), isActive: (e) => e.isActive('heading', { level: 3 }), icon: Heading3, label: 'Subjudul' },
    { action: (e) => e.chain().focus().toggleBulletList().run(), isActive: (e) => e.isActive('bulletList'), icon: List, label: 'Daftar' },
    { action: (e) => e.chain().focus().toggleOrderedList().run(), isActive: (e) => e.isActive('orderedList'), icon: ListOrdered, label: 'Daftar Bernomor' },
    { action: (e) => e.chain().focus().toggleBlockquote().run(), isActive: (e) => e.isActive('blockquote'), icon: Quote, label: 'Kutipan' },
];
</script>

<template>
    <div :class="cn('rounded-md border border-input bg-background', props.class)">
        <div v-if="editor" class="flex flex-wrap items-center gap-0.5 border-b border-border p-1.5">
            <button
                v-for="item in buttons"
                :key="item.label"
                type="button"
                :title="item.label"
                class="inline-flex h-8 w-8 items-center justify-center rounded transition-colors hover:bg-muted"
                :class="item.isActive(editor) ? 'bg-primary/10 text-primary' : 'text-muted-foreground'"
                @mousedown.prevent
                @click="item.action(editor)"
            >
                <component :is="item.icon" class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Tautan"
                class="inline-flex h-8 w-8 items-center justify-center rounded transition-colors hover:bg-muted"
                :class="editor.isActive('link') ? 'bg-primary/10 text-primary' : 'text-muted-foreground'"
                @mousedown.prevent
                @click="setLink"
            >
                <Link2 class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Sisipkan Gambar"
                class="inline-flex h-8 w-8 items-center justify-center rounded text-muted-foreground transition-colors hover:bg-muted"
                @mousedown.prevent
                @click="insertImage"
            >
                <ImagePlus class="h-4 w-4" />
            </button>
            <span class="mx-1 h-5 w-px bg-border" />
            <button
                type="button"
                title="Urungkan"
                class="inline-flex h-8 w-8 items-center justify-center rounded text-muted-foreground transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                :disabled="!editor.can().undo()"
                @mousedown.prevent
                @click="editor.chain().focus().undo().run()"
            >
                <Undo2 class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Ulangi"
                class="inline-flex h-8 w-8 items-center justify-center rounded text-muted-foreground transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                :disabled="!editor.can().redo()"
                @mousedown.prevent
                @click="editor.chain().focus().redo().run()"
            >
                <Redo2 class="h-4 w-4" />
            </button>
        </div>
        <EditorContent :editor="editor" />
    </div>
</template>
