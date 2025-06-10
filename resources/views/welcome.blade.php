<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gestor de Tareas</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        /* ! tailwindcss v3.2.4 | MIT License | https://tailwindcss.com */
        *,
        ::after,
        ::before {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #e5e7eb
        }

        ::after,
        ::before {
            --tw-content: ''
        }

        html {
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -moz-tab-size: 4;
            tab-size: 4;
            font-family: Figtree, sans-serif;
            font-feature-settings: normal
        }

        body {
            margin: 0;
            line-height: inherit
        }

        hr {
            height: 0;
            color: inherit;
            border-top-width: 1px
        }

        abbr:where([title]) {
            -webkit-text-decoration: underline dotted;
            text-decoration: underline dotted
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: inherit;
            font-weight: inherit
        }

        a {
            color: inherit;
            text-decoration: inherit
        }

        b,
        strong {
            font-weight: bolder
        }

        code,
        kbd,
        pre,
        samp {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 1em
        }

        small {
            font-size: 80%
        }

        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline
        }

        sub {
            bottom: -.25em
        }

        sup {
            top: -.5em
        }

        table {
            text-indent: 0;
            border-color: inherit;
            border-collapse: collapse
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            font-size: 100%;
            font-weight: inherit;
            line-height: inherit;
            color: inherit;
            margin: 0;
            padding: 0
        }

        button,
        select {
            text-transform: none
        }

        [type=button],
        [type=reset],
        [type=submit],
        button {
            -webkit-appearance: button;
            background-color: transparent;
            background-image: none
        }

        :-moz-focusring {
            outline: auto
        }

        :-moz-ui-invalid {
            box-shadow: none
        }

        progress {
            vertical-align: baseline
        }

        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto
        }

        [type=search] {
            -webkit-appearance: textfield;
            outline-offset: -2px
        }

        ::-webkit-search-decoration {
            -webkit-appearance: none
        }

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            font: inherit
        }

        summary {
            display: list-item
        }

        blockquote,
        dd,
        dl,
        figure,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        p,
        pre {
            margin: 0
        }

        fieldset {
            margin: 0;
            padding: 0
        }

        legend {
            padding: 0
        }

        menu,
        ol,
        ul {
            list-style: none;
            margin: 0;
            padding: 0
        }

        textarea {
            resize: vertical
        }

        input::placeholder,
        textarea::placeholder {
            opacity: 1;
            color: #9ca3af
        }

        [role=button],
        button {
            cursor: pointer
        }

        :disabled {
            cursor: default
        }

        audio,
        canvas,
        embed,
        iframe,
        img,
        object,
        svg,
        video {
            display: block;
            vertical-align: middle
        }

        img,
        video {
            max-width: 100%;
            height: auto
        }

        [hidden] {
            display: none
        }

        *,
        ::before,
        ::after {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia:
        }

        ::-webkit-backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia:
        }

        ::backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia:
        }

        .relative {
            position: relative
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto
        }

        .mx-6 {
            margin-left: 1.5rem;
            margin-right: 1.5rem
        }

        .ml-4 {
            margin-left: 1rem
        }

        .mt-16 {
            margin-top: 4rem
        }

        .mt-6 {
            margin-top: 1.5rem
        }

        .mt-4 {
            margin-top: 1rem
        }

        .-mt-px {
            margin-top: -1px
        }

        .mr-1 {
            margin-right: 0.25rem
        }

        .flex {
            display: flex
        }

        .inline-flex {
            display: inline-flex
        }

        .grid {
            display: grid
        }

        .h-16 {
            height: 4rem
        }

        .h-7 {
            height: 1.75rem
        }

        .h-6 {
            height: 1.5rem
        }

        .h-5 {
            height: 1.25rem
        }

        .min-h-screen {
            min-height: 100vh
        }

        .w-auto {
            width: auto
        }

        .w-16 {
            width: 4rem
        }

        .w-7 {
            width: 1.75rem
        }

        .w-6 {
            width: 1.5rem
        }

        .w-5 {
            width: 1.25rem
        }

        .max-w-7xl {
            max-width: 80rem
        }

        .shrink-0 {
            flex-shrink: 0
        }

        .scale-100 {
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr))
        }

        .items-center {
            align-items: center
        }

        .justify-center {
            justify-content: center
        }

        .gap-6 {
            gap: 1.5rem
        }

        .gap-4 {
            gap: 1rem
        }

        .self-center {
            align-self: center
        }

        .rounded-lg {
            border-radius: 0.5rem
        }

        .rounded-full {
            border-radius: 9999px
        }

        .bg-gray-100 {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity))
        }

        .bg-white {
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity))
        }

        .bg-red-50 {
            --tw-bg-opacity: 1;
            background-color: rgb(254 242 242 / var(--tw-bg-opacity))
        }

        .bg-dots-darker {
            background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E")
        }

        .from-gray-700\/50 {
            --tw-gradient-from: rgb(55 65 81 / 0.5);
            --tw-gradient-to: rgb(55 65 81 / 0);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
        }

        .via-transparent {
            --tw-gradient-to: rgb(0 0 0 / 0);
            --tw-gradient-stops: var(--tw-gradient-from), transparent, var(--tw-gradient-to)
        }

        .bg-center {
            background-position: center
        }

        .stroke-red-500 {
            stroke: #ef4444
        }

        .stroke-gray-400 {
            stroke: #9ca3af
        }

        .p-6 {
            padding: 1.5rem
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem
        }

        .text-center {
            text-align: center
        }

        .text-right {
            text-align: right
        }

        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem
        }

        .font-semibold {
            font-weight: 600
        }

        .leading-relaxed {
            line-height: 1.625
        }

        .text-gray-600 {
            --tw-text-opacity: 1;
            color: rgb(75 85 99 / var(--tw-text-opacity))
        }

        .text-gray-900 {
            --tw-text-opacity: 1;
            color: rgb(17 24 39 / var(--tw-text-opacity))
        }

        .text-gray-500 {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity))
        }

        .underline {
            -webkit-text-decoration-line: underline;
            text-decoration-line: underline
        }

        .antialiased {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

        .shadow-2xl {
            --tw-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            --tw-shadow-colored: 0 25px 50px -12px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)
        }

        .shadow-gray-500\/20 {
            --tw-shadow-color: rgb(107 114 128 / 0.2);
            --tw-shadow: var(--tw-shadow-colored)
        }

        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms
        }

        .selection\:bg-red-500 *::selection {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity))
        }

        .selection\:text-white *::selection {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity))
        }

        .selection\:bg-red-500::selection {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity))
        }

        .selection\:text-white::selection {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity))
        }

        .hover\:text-gray-900:hover {
            --tw-text-opacity: 1;
            color: rgb(17 24 39 / var(--tw-text-opacity))
        }

        .hover\:text-gray-700:hover {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity))
        }

        .focus\:rounded-sm:focus {
            border-radius: 0.125rem
        }

        .focus\:outline:focus {
            outline-style: solid
        }

        .focus\:outline-2:focus {
            outline-width: 2px
        }

        .focus\:outline-red-500:focus {
            outline-color: #ef4444
        }

        .group:hover .group-hover\:stroke-gray-600 {
            stroke: #4b5563
        }

        .z-10 {
            z-index: 10
        }

        @media (prefers-reduced-motion: no-preference) {
            .motion-safe\:hover\:scale-\[1\.01\]:hover {
                --tw-scale-x: 1.01;
                --tw-scale-y: 1.01;
                transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
            }
        }

        @media (prefers-color-scheme: dark) {
            .dark\:bg-gray-900 {
                --tw-bg-opacity: 1;
                background-color: rgb(17 24 39 / var(--tw-bg-opacity))
            }

            .dark\:bg-gray-800\/50 {
                background-color: rgb(31 41 55 / 0.5)
            }

            .dark\:bg-red-800\/20 {
                background-color: rgb(153 27 27 / 0.2)
            }

            .dark\:bg-dots-lighter {
                background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E")
            }

            .dark\:bg-gradient-to-bl {
                background-image: linear-gradient(to bottom left, var(--tw-gradient-stops))
            }

            .dark\:stroke-gray-600 {
                stroke: #4b5563
            }

            .dark\:text-gray-400 {
                --tw-text-opacity: 1;
                color: rgb(156 163 175 / var(--tw-text-opacity))
            }

            .dark\:text-white {
                --tw-text-opacity: 1;
                color: rgb(255 255 255 / var(--tw-text-opacity))
            }

            .dark\:shadow-none {
                --tw-shadow: 0 0 #0000;
                --tw-shadow-colored: 0 0 #0000;
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)
            }

            .dark\:ring-1 {
                --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
                --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
                box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)
            }

            .dark\:ring-inset {
                --tw-ring-inset: inset
            }

            .dark\:ring-white\/5 {
                --tw-ring-color: rgb(255 255 255 / 0.05)
            }

            .dark\:hover\:text-white:hover {
                --tw-text-opacity: 1;
                color: rgb(255 255 255 / var(--tw-text-opacity))
            }

            .group:hover .dark\:group-hover\:stroke-gray-400 {
                stroke: #9ca3af
            }
        }

        @media (min-width: 640px) {
            .sm\:fixed {
                position: fixed
            }

            .sm\:top-0 {
                top: 0px
            }

            .sm\:right-0 {
                right: 0px
            }

            .sm\:ml-0 {
                margin-left: 0px
            }

            .sm\:flex {
                display: flex
            }

            .sm\:items-center {
                align-items: center
            }

            .sm\:justify-center {
                justify-content: center
            }

            .sm\:justify-between {
                justify-content: space-between
            }

            .sm\:text-left {
                text-align: left
            }

            .sm\:text-right {
                text-align: right
            }
        }

        @media (min-width: 768px) {
            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media (min-width: 1024px) {
            .lg\:gap-8 {
                gap: 2rem
            }

            .lg\:p-8 {
                padding: 2rem
            }
        }
    </style>
</head>

<body class="antialiased">
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log
                        in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <a href="https://miro.com/app/board/uXjVLl8VYdo=/" target="_blank"
                        class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center rounded-full"
                                style="background-color:rgb(90, 141, 238);">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff"
                                    class="size-6">
                                    <path
                                        d="M12 .75a8.25 8.25 0 0 0-4.135 15.39c.686.398 1.115 1.008 1.134 1.623a.75.75 0 0 0 .577.706c.352.083.71.148 1.074.195.323.041.6-.218.6-.544v-4.661a6.714 6.714 0 0 1-.937-.171.75.75 0 1 1 .374-1.453 5.261 5.261 0 0 0 2.626 0 .75.75 0 1 1 .374 1.452 6.712 6.712 0 0 1-.937.172v4.66c0 .327.277.586.6.545.364-.047.722-.112 1.074-.195a.75.75 0 0 0 .577-.706c.02-.615.448-1.225 1.134-1.623A8.25 8.25 0 0 0 12 .75Z" />
                                    <path fill-rule="evenodd"
                                        d="M9.013 19.9a.75.75 0 0 1 .877-.597 11.319 11.319 0 0 0 4.22 0 .75.75 0 1 1 .28 1.473 12.819 12.819 0 0 1-4.78 0 .75.75 0 0 1-.597-.876ZM9.754 22.344a.75.75 0 0 1 .824-.668 13.682 13.682 0 0 0 2.844 0 .75.75 0 1 1 .156 1.492 15.156 15.156 0 0 1-3.156 0 .75.75 0 0 1-.668-.824Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">¿Cómo funciona un gestor de tareas?
                            </h2>
                            <p class="mt-4 text-gray-500 text-sm leading-relaxed" style="text-align: justify;">
                                La aplicación web como gestor de tareas ayuda al usuario a enviar sus solicitudes al
                                área requerida,
                                el supervisor del área revisa y autoriza el paso de éstas hacia las zonas de
                                procesamiento,
                                donde los operadores propietarios de cada tarea se encargan de procesarlas,
                                intercambiando comentarios
                                con los participantes como retroalimentación mutua.
                            </p>
                        </div>
                    </a>

                    <a href="https://www.ibm.com/mx-es/topics/workflow" target="_blank"
                        class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center rounded-full"
                                style="background-color:rgb(90, 141, 238);">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 480" fill="#ffffff"
                                    xml:space="preserve">
                                    <path
                                        d="M240 0C107.664 0 0 107.664 0 240s107.664 240 240 240 240-107.664 240-240S372.336 0 240 0m0 460c-121.309 0-220-98.691-220-220S118.691 20 240 20s220 98.691 220 220-98.691 220-220 220" />
                                    <path
                                        d="M410 194.999h-27.058c-2.643-8.44-6-16.56-10.03-24.271l19.158-19.158a19.84 19.84 0 0 0 5.854-14.121c0-5.332-2.08-10.347-5.854-14.121l-35.399-35.399a19.84 19.84 0 0 0-14.122-5.854 19.84 19.84 0 0 0-14.121 5.854l-19.158 19.158a148 148 0 0 0-24.271-10.03V70c0-11.028-8.972-20-20-20h-50c-11.028 0-20 8.972-20 20v27.058c-8.44 2.643-16.56 6-24.271 10.03L151.57 87.93a19.84 19.84 0 0 0-14.121-5.854 19.84 19.84 0 0 0-14.121 5.854l-35.399 35.399a19.84 19.84 0 0 0-5.854 14.122 19.84 19.84 0 0 0 5.854 14.121l19.158 19.158a148 148 0 0 0-10.03 24.271H70c-11.028 0-20 8.972-20 20v50c0 11.028 8.972 20 20 20h27.057c2.643 8.44 6 16.56 10.03 24.271L87.929 328.43a19.84 19.84 0 0 0-5.854 14.121c0 5.332 2.08 10.347 5.854 14.121l35.399 35.399a19.84 19.84 0 0 0 14.122 5.854 19.84 19.84 0 0 0 14.121-5.854l19.158-19.158A148 148 0 0 0 195 382.943V410c0 11.028 8.972 20 20 20h50c11.028 0 20-8.972 20.001-20v-27.058c8.44-2.643 16.56-6 24.271-10.03l19.158 19.158a19.84 19.84 0 0 0 14.121 5.854c5.332 0 10.347-2.08 14.121-5.854l35.399-35.399a19.84 19.84 0 0 0 5.854-14.122 19.84 19.84 0 0 0-5.854-14.121l-19.158-19.158a148 148 0 0 0 10.03-24.271H410c11.028 0 20-8.972 20-20v-50c0-11.028-8.972-20-20-20m0 69.999h-34.598a10 10 0 0 0-9.684 7.503c-3.069 11.901-7.716 23.133-13.813 33.387a10 10 0 0 0 1.524 12.182l24.5 24.457-35.357 35.4-24.5-24.5a10 10 0 0 0-12.182-1.524c-10.254 6.097-21.487 10.745-33.387 13.813A10 10 0 0 0 265 375.4V410h-50v-34.599a10 10 0 0 0-7.503-9.684c-11.901-3.069-23.133-7.716-33.387-13.813a10 10 0 0 0-5.107-1.404 9.98 9.98 0 0 0-7.073 2.931l-24.457 24.5-35.4-35.357 24.5-24.5a10 10 0 0 0 1.524-12.182c-6.097-10.254-10.745-21.487-13.813-33.387a10 10 0 0 0-9.684-7.503H70v-50h34.596a10 10 0 0 0 9.684-7.503c3.069-11.901 7.716-23.133 13.813-33.387a10 10 0 0 0-1.524-12.182l-24.5-24.457 35.357-35.4 24.5 24.5a10 10 0 0 0 1.524-12.182c6.097-10.254 10.745-21.487 13.813-33.387a10 10 0 0 0 9.684-7.503H410z" />
                                    <path
                                        d="m331.585 292.475-40-35-13.17 15.051L298.386 290H240c-27.57 0-50-22.43-50-50h-20c0 38.598 31.402 70 70 70h58.386l-19.971 17.475 13.17 15.051 40-35a10 10 0 0 0 0-15.051m-130-85.002L181.614 190H240c27.57 0 50 22.43 50 50h20c0-38.598-31.402-70-70-70h-58.386l19.971-17.475-13.17-15.051-40 35a10 10 0 0 0 0 15.05l40 35z" />
                                </svg>
                            </div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">¿Qué es un flujo de trabajo?</h2>
                            <p class="mt-4 text-gray-500 text-sm leading-relaxed" style="text-align: justify;">
                                Es un sistema para gestionar procesos y tareas repetitivos que ocurren en un orden
                                particular.
                                Es el mecanismo por el cual las personas y las organizaciones realizan su trabajo, ya
                                sea fabricando un
                                producto, proporcionando un servicio, procesando información o cualquier otra actividad
                                generadora
                                de valor. Un flujo de trabajo puede diseñarse en su modalidad secuencial o en su
                                modalidad dinámica
                                la cual consiste en hacer fluir tareas de una solicitud en forma simultánea, en donde
                                los operadores
                                las procesan sin una secuencia definida.
                            </p>
                        </div>
                    </a>

                    <a href="https://dle.rae.es/incidencia" target="_blank"
                        class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center rounded-full"
                                style="background-color:rgb(90, 141, 238);">
                                <svg width="800" height="800" viewBox="0 0 24 24" fill="#ffffff"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M14.94 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16m3.92 10.8-1.12 1.12a.4.4 0 0 1-.57 0l-2.73-2.73a2.77 2.77 0 0 1-3.38-3.91l1.88 1.86 1.13-1.13-1.88-1.87a2.77 2.77 0 0 1 3.93 3.37l2.73 2.77a.4.4 0 0 1 0 .52z" />
                                    <path
                                        d="M2.94 12a6 6 0 0 1 4-5.65V4.28a8 8 0 0 0 0 15.46v-2.09a6 6 0 0 1-2.9-2.19A6 6 0 0 1 2.94 12" />
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900">¿Qué es una incidencia?</h2>
                            <p class="mt-4 text-gray-500 text-sm leading-relaxed" style="text-align: justify;">
                                Es el procesamiento repetitivo de una misma tarea, cada vez que dicha tarea vuelve a
                                procesarse,
                                se registra como incidencia. Las incidencias sirven de indicadores para los informes
                                productividad,
                                niveles de calidad de las tareas que circulan en el flujo de trabajo.
                            </p>
                        </div>
                    </a>

                    <a href="https://www.ibm.com/mx-es/topics/automation" target="_blank">
                        <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                            <div>
                                <div class="h-16 w-16 flex items-center justify-center rounded-full"
                                    style="background-color: rgb(90, 141, 238);">
                                    <svg style="width: 100%; height: 65%;" viewBox="0 -0.25 67 67" fill="#ffffff"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M18.947 21.394C23.18 31.567 27.575 42.131 32.035 52.852c2.968 0.698 3.622 1.375 4.037 4.703 0.197 1.573 0.305 3.155 0.326 4.739 0.035 2.874 -1.17 4.207 -4.031 4.173 -4.915 -0.058 -9.829 -0.317 -14.744 -0.431 -1.962 -0.045 -3.932 0.017 -5.892 0.128 -1.464 0.083 -2.297 -0.298 -2.415 -1.726a64.5 64.5 0 0 1 -0.28 -8.175c0.103 -2.273 1.56 -3.382 3.962 -3.538 0.762 -0.05 1.53 -0.025 2.295 -0.029 0.758 -0.004 1.517 0 2.546 0 -0.548 -1.972 -0.996 -3.793 -1.562 -5.577 -2.029 -6.394 -4.098 -12.777 -6.152 -19.163 -0.066 -0.207 -0.159 -0.408 -0.211 -0.619 -0.371 -1.481 -0.378 -3.221 -1.206 -4.37 -0.759 -1.053 -2.458 -1.387 -3.647 -2.179C-1.153 16.645 -1.546 6.707 4.286 2.029c3.703 -2.97 10.291 -2.646 14.308 0.752a10.7 10.7 0 0 1 2.106 2.271 2.11 2.11 0 0 0 2.162 1.08c6.426 -0.45 12.859 -0.817 19.279 -1.335 3.88 -0.314 3.873 -0.413 5.259 3.196 1.917 -0.221 3.851 -0.41 5.77 -0.696 0.366 -0.055 0.737 -0.447 0.998 -0.77 1.062 -1.317 2.092 -2.662 3.118 -4.009 1.335 -1.756 2.523 -2.093 4.559 -1.205a29 29 0 0 1 3.099 1.488c1.177 0.691 1.568 1.795 1.137 2.756 -0.426 0.952 -1.524 1.401 -2.809 1.042a6.75 6.75 0 0 1 -1.942 -0.844c-0.938 -0.632 -1.575 -0.406 -2.211 0.421q-0.676 0.826 -1.448 1.562c-1.691 1.713 -1.784 3.304 -0.348 5.251a122 122 0 0 1 3.019 4.285c0.508 0.755 1.078 0.92 1.947 0.637a9.8 9.8 0 0 1 2.872 -0.577c0.525 -0.005 1.409 0.479 1.494 0.877 0.115 0.538 -0.208 1.488 -0.653 1.767a33 33 0 0 1 -4.518 2.295c-1.313 0.563 -2.378 -0.099 -3.127 -1.137 -1.342 -1.858 -2.677 -3.732 -3.84 -5.702 -0.528 -0.894 -1.105 -1.221 -2.067 -1.225a20.5 20.5 0 0 1 -3.267 -0.197c-1.157 -0.193 -1.692 0.329 -2.107 1.293 -0.868 2.022 -0.939 1.956 -3.185 1.972 -3.057 0.023 -6.115 0.297 -9.17 0.263 -3.6 -0.041 -7.202 -0.253 -10.796 -0.492 -0.97 -0.066 -1.599 0.033 -2.18 0.923 -0.739 1.133 -1.711 2.116 -2.797 3.42m0.827 -9.034C19.665 7.114 15.822 3.404 10.667 3.571c-4.152 0.135 -7.098 3.413 -7.04 7.832 0.06 4.599 3.33 7.883 7.915 7.729C16.651 18.96 20.2 15.18 19.773 12.36m-7.565 10.505q0 0.362 0.075 0.717c0.85 2.416 1.742 4.816 2.568 7.24 2.372 6.955 4.707 13.922 7.102 20.868 0.164 0.471 0.664 1.113 1.069 1.161 1.271 0.15 2.571 0.055 4.011 0.055L15.626 22.23zm11.373 -8.407 20.49 -0.576c-0.146 -0.715 -0.259 -1.233 -0.358 -1.754a70.5 70.5 0 0 1 -0.32 -1.77c-0.289 -1.747 -0.286 -1.748 -2.067 -1.742l-16.205 0.059c-0.683 0.003 -1.367 0 -2.063 0 0.17 1.882 0.327 3.607 0.524 5.782m-10.725 47.554h18.791v-5.182H13.165z" />
                                        <path
                                            d="M11.085 15.041c-1.816 -0.025 -3.382 -0.985 -3.788 -2.325 -0.48 -1.585 0.182 -3.35 1.673 -4.454 2.685 -1.989 5.458 -1.282 6.341 1.617 0.823 2.709 -1.218 5.202 -4.228 5.16" />
                                    </svg>
                                </div>
                                <h2 class="mt-6 text-xl font-semibold text-gray-900">Automatización</h2>
                                <p class="mt-4 text-gray-500 text-sm leading-relaxed" style="text-align: justify;">
                                    Los procesos manuales son susceptibles a errores humanos, ineficiencias e
                                    incongruencias que pueden
                                    interrumpir la calidad del producto y las experiencias del cliente, los sistemas
                                    automatizados son
                                    inherentemente eficientes, sistemáticos y escalables, y permiten a los usuarios del
                                    sistema enfocarse
                                    en más tareas de análisis, tácticas, estrategias de mejora, sostenibilidad y
                                    crecimiento.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">
                <div class="text-center text-sm sm:text-left">
                    &nbsp;
                </div>

                <div class="text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                <p class="text-sm text-gray-500">Versión {{ config('app.version') }}</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
