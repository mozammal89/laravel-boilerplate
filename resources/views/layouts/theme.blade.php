<script>
    function hexToRgb(hex) {
        // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
        var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
        hex = hex.replace(shorthandRegex, function (m, r, g, b) {
            return r + r + g + g + b + b;
        });

        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    function changeTheme() {
        localStorage.setItem("primary", '{{ Session::get('app_cfg_data', [])['primary_color'] }}');
        localStorage.setItem("secondary", '{{ Session::get('app_cfg_data', [])['secondary_color'] }}');

        let m_rgb = hexToRgb(localStorage.getItem("primary"));

        if (localStorage.getItem("primary") != null) {
            document.documentElement.style.setProperty(
                "--theme-deafult",
                localStorage.getItem("primary")
            );
            document.documentElement.style.setProperty(
                "--menu-bg-color",
                `rgba(${m_rgb.r}, ${m_rgb.g}, ${m_rgb.b}, 0.07)`
            );
            document.documentElement.style.setProperty(
                "--menu-bg-hover",
                `rgba(${m_rgb.r}, ${m_rgb.g}, ${m_rgb.b}, 0.12)`
            );
        }
        if (localStorage.getItem("secondary") != null) {
            document.documentElement.style.setProperty(
                "--theme-secondary",
                localStorage.getItem("secondary")
            );
        }
    }

    changeTheme();
</script>
