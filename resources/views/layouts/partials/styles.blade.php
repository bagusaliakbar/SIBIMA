        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }
            .sidebar-link { transition: all 0.2s ease-in-out; }
            .sidebar-link:hover { color: white; background-color: rgba(255, 255, 255, 0.05); }

            /* ========================================================
               MINIMIZED / COLLAPSED SIDEBAR (EXPAND ON HOVER)
               ======================================================== */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) {
                width: 80px !important;
            }

            aside.sidebar-hover-expanded {
                width: 280px !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
                z-index: 50 !important;
            }

            /* Smooth width, transform, & shadow transitions */
            aside {
                transition-property: width, transform, box-shadow;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }

            /* Hide text labels, descriptions, and dropdown arrows only when collapsed AND not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-text,
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-arrow,
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-header-title,
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-section-title {
                display: none !important;
            }

            /* Center icons when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-link {
                font-size: 0 !important;
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin-left: auto !important;
                margin-right: auto !important;
                width: 44px !important;
                height: 44px !important;
                border-radius: 12px !important;
                position: relative !important;
            }

            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-link svg {
                margin-right: 0 !important;
                width: 20px !important;
                height: 20px !important;
                flex-shrink: 0 !important;
            }

            /* Hide dropdown submenu children when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-dropdown-content {
                display: none !important;
            }

            /* Section headers become minimalist pill separators when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-section-header {
                padding-left: 0 !important;
                padding-right: 0 !important;
                text-align: center !important;
                margin-top: 1.25rem !important;
                margin-bottom: 0.5rem !important;
                border-top: none !important;
            }

            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-section-divider {
                display: block !important;
            }

            /* Mini notification ping badge on icon when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-badge {
                position: absolute !important;
                top: 6px !important;
                right: 6px !important;
                padding: 0 !important;
                width: 8px !important;
                height: 8px !important;
                min-width: 8px !important;
                border-radius: 9999px !important;
                font-size: 0 !important;
                line-height: 0 !important;
            }
            /* Date & Time Picker Calendar / Clock Icons */
            input[type="date"],
            input[type="time"],
            input[type="datetime-local"] {
                color-scheme: light;
            }

            input[type="date"]::-webkit-calendar-picker-indicator,
            input[type="time"]::-webkit-calendar-picker-indicator,
            input[type="datetime-local"]::-webkit-calendar-picker-indicator {
                cursor: pointer;
                opacity: 0.75;
                transition: all 0.2s ease-in-out;
            }

            input[type="date"]::-webkit-calendar-picker-indicator:hover,
            input[type="time"]::-webkit-calendar-picker-indicator:hover,
            input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
                opacity: 1;
            }

            /* Dark Mode: Crisp White / Bright Calendar & Clock Icons */
            .dark input[type="date"],
            .dark input[type="time"],
            .dark input[type="datetime-local"] {
                color-scheme: dark !important;
            }

            .dark input[type="date"]::-webkit-calendar-picker-indicator,
            .dark input[type="time"]::-webkit-calendar-picker-indicator,
            .dark input[type="datetime-local"]::-webkit-calendar-picker-indicator {
                filter: none !important;
                opacity: 0.9 !important;
                cursor: pointer;
            }

            .dark input[type="date"]::-webkit-calendar-picker-indicator:hover,
            .dark input[type="time"]::-webkit-calendar-picker-indicator:hover,
            .dark input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
                opacity: 1 !important;
                filter: drop-shadow(0 0 2px rgba(249, 115, 22, 0.7)) !important;
            }

            /* Custom Sleek Scrollbar (Light, Dark, & Dark Sidebar aware) */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(100, 116, 139, 0.28);
                border-radius: 9999px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(100, 116, 139, 0.48);
            }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb,
            aside .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.16);
            }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
            aside .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.34);
            }

            /* Soft Bordered Table & Sticky Header (Scoped, solid opaque sticky cells) */
            .overflow-x-auto table thead {
                position: sticky;
                top: 0;
                z-index: 10;
            }
            :where(.overflow-x-auto table thead th) {
                background-color: #f8fafc;
                box-shadow: inset 0 -1px 0 rgba(226, 232, 240, 0.9);
            }
            :where(.dark .overflow-x-auto table thead th) {
                background-color: #0f172a;
                box-shadow: inset 0 -1px 0 rgba(51, 65, 85, 0.85);
            }
            table tbody tr {
                transition: background-color 0.15s ease-in-out;
            }
            table tbody tr:hover {
                background-color: rgba(241, 245, 249, 0.75);
            }
            .dark table tbody tr:hover {
                background-color: rgba(30, 41, 59, 0.7);
            }
        </style>
