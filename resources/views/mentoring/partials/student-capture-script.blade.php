    <script src="{{ asset('assets/vendor/html2canvas/html2canvas.min.js') }}"></script>
    <script>
        function captureProgress() {
            const element = document.getElementById('progress-card');
            const isDarkMode = document.documentElement.classList.contains('dark');
            
            // Define colors based on theme
            const bgColor = isDarkMode ? '#0f172a' : '#ffffff';
            
            html2canvas(element, {
                scale: 2, // High quality
                useCORS: true,
                backgroundColor: bgColor,
                logging: false,
                onclone: (clonedDoc) => {
                    const clonedElement = clonedDoc.getElementById('progress-card');
                    clonedElement.style.padding = '32px';
                    clonedElement.style.background = bgColor;
                    clonedElement.style.borderRadius = '0px'; // Clean edges for export
                    
                    // Match the theme class on cloned document
                    if (isDarkMode) {
                        clonedDoc.documentElement.classList.add('dark');
                    } else {
                        clonedDoc.documentElement.classList.remove('dark');
                    }
                }
            }).then(canvas => {
                const link = document.createElement('a');
                const timestamp = new Date().toISOString().split('T')[0];
                link.download = `Bukti-ACC-Mentoring-{{ Auth::user()->name }}-${timestamp}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }

        // Deep link handler for mentoring session notification highlights
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight') || window.location.hash.replace('#session-', '');

            if (highlightId) {
                const row = document.getElementById('session-' + highlightId);
                if (row) {
                    setTimeout(() => {
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        row.classList.add('ring-2', 'ring-orange-500', 'bg-orange-50', 'dark:bg-orange-950/40');
                        setTimeout(() => {
                            row.classList.remove('ring-2', 'ring-orange-500', 'bg-orange-50', 'dark:bg-orange-950/40');
                        }, 4000);
                    }, 300);
                }
            }
        });
    </script>
