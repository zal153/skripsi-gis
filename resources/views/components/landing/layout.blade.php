<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Posyandu Locator' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />

    <!-- Tailwind CSS & App JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
        }

        /* Sidebar glass */
        .sidebar {
            position: relative;
            height: 100vh;
            width: 350px;
            min-width: 350px;
            max-width: 350px;
            background: #ffffff;
            z-index: 999;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            padding: 24px 20px;
            gap: 16px;
            border-right: 1px solid rgba(229, 231, 235, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                top: auto;
                left: 0;
                right: 0;
                bottom: 0;
                height: 50vh;
                width: 100%;
                min-width: 100%;
                max-width: 100%;
                border-right: none;
                border-top: 1px solid rgba(229, 231, 235, 0.8);
                box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
                border-radius: 20px 20px 0 0;
            }
        }

        .btn-primary {
            background: #111827;
            /* slate-900 */
            color: #fff;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(17, 24, 39, 0.15);
            width: 100%;
        }

        .btn-primary:hover {
            background: #1f2937;
            /* slate-800 */
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 24, 39, 0.25);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-danger {
            background: #ef4444;
            /* red-500 */
            color: #fff;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
            width: 100%;
        }

        .btn-danger:hover {
            background: #dc2626;
            /* red-600 */
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.25);
        }

        .btn-danger:active {
            transform: translateY(0);
        }

        .result-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px;
            border: 1px solid rgba(229, 231, 235, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.04);
            border-color: rgba(124, 58, 237, 0.3);
        }

        .result-card.active {
            border-color: #7c3aed;
            background: rgba(124, 58, 237, 0.03);
            box-shadow: 0 6px 15px rgba(124, 58, 237, 0.06);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex: 1;
            color: #9ca3af;
            text-align: center;
        }

        .pulse-ring {
            animation: pulse-ring 1.5s ease-out infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.9);
                opacity: 1;
            }

            100% {
                transform: scale(1.6);
                opacity: 0;
            }
        }

        .slide-in {
            animation: slideIn 0.3s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Leaflet custom marker */
        .custom-marker {
            background: #e53e3e;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .user-marker {
            background: #1d6eea;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(29, 110, 234, 0.5);
        }

        .badge-distance {
            background: #f5f3ff;
            color: #7c3aed;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .feedback-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #4b5563;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            padding: 10px;
            border-radius: 12px;
            transition: all 0.2s;
            border: 1px solid rgba(229, 231, 235, 0.6);
            background: rgba(255, 255, 255, 0.6);
            width: 100%;
        }

        .feedback-btn:hover {
            background: #fff;
            color: #1f2937;
            border-color: rgba(124, 58, 237, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .loading-dots span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #7c3aed;
            border-radius: 50%;
            margin: 0 2px;
            animation: bounce 1.2s infinite;
        }

        .loading-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .loading-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-8px);
            }
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(124, 58, 237, 0.3);
            border-radius: 99px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(124, 58, 237, 0.5);
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.3);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .modal-box {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            width: 360px;
            max-width: 90vw;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
            transform: scale(0.95);
            transition: transform 0.2s;
            border: 1px solid rgba(229, 231, 235, 0.8);
        }

        .modal-overlay.active .modal-box {
            transform: scale(1);
        }

        /* Map Layer Switcher Styles */
        .map-layer-switcher {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 999;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(229, 231, 235, 0.8);
            border-radius: 12px;
            padding: 4px;
            display: flex;
            gap: 4px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .layer-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: #4b5563;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .layer-btn:hover {
            background: rgba(255, 255, 255, 0.6);
            color: #1f2937;
        }

        .layer-btn.active {
            background: #111827;
            /* slate-900 */
            color: #fff;
            box-shadow: 0 4px 10px rgba(17, 24, 39, 0.2);
        }

        /* Transport Selector Styles */
        .transport-selector {
            display: flex;
            justify-content: space-around;
            align-items: center;
            border-top: 1px solid rgba(229, 231, 235, 0.8);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
            padding: 12px 0;
            width: 100%;
            gap: 8px;
        }

        .transport-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #6b7280;
            /* gray-500 */
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            gap: 4px;
        }

        .transport-btn i {
            font-size: 20px;
        }

        .transport-btn:hover {
            color: #111827;
            /* gray-900 */
        }

        .transport-btn.active {
            color: #7c3aed;
            /* active purple */
            background: #f5f3ff;
        }

        /* Telemetry Card Styles */
        .telemetry-card {
            transition: all 0.3s ease;
        }

        .result-action-btn {
            background: #7c3aed;
            border: none;
            color: #fff;
            width: 32px;
            height: 32px;
            min-width: 32px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(124, 58, 237, 0.2);
        }

        .result-action-btn:hover {
            background: #6d28d9;
            transform: scale(1.08);
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }

        /* Custom range slider thumb */
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #111827;
            cursor: pointer;
            transition: transform 0.1s;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }

        /* Prominent Active Destination Marker */
        .active-destination-marker {
            position: relative;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .destination-pin {
            position: absolute;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
            border: 2px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: bounce-pin 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .destination-icon-inner {
            transform: rotate(45deg);
            color: white;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes bounce-pin {
            0% {
                transform: translateY(-30px) rotate(-45deg);
                opacity: 0;
            }

            60% {
                transform: translateY(5px) rotate(-45deg);
            }

            80% {
                transform: translateY(-3px) rotate(-45deg);
            }

            100% {
                transform: translateY(0) rotate(-45deg);
                opacity: 1;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="relative overflow-hidden">
    {{ $slot }}

    @stack('scripts')
</body>

</html>
