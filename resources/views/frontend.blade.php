<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocGov</title>
    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
        rel="stylesheet"
    />
    <!-- MDB -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.1.0/mdb.min.css"
        rel="stylesheet"
    />
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="/resources/tabler-icons/tabler-icons.min.css">
</head>
<body>
<!-- Header -->
<li class="nav-item align-items-center d-flex" style="position: absolute">
    <i class="ti ti-sun"></i>
    <!-- Default switch -->
    <div class="ms-2 form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="themingSwitcher"/>
    </div>
    <i class="ti ti-moon"></i>
</li>
<script>
    const themeStitcher = document.getElementById("themingSwitcher");
    const isSystemThemeSetToDark = window.matchMedia("(prefers-color-scheme: dark)").matches;

    // Retrieve saved theme from localStorage
    const savedTheme = localStorage.getItem("theme");

    // Set theme based on saved preference or system theme
    if (savedTheme) {
        themeStitcher.checked = savedTheme === "dark";
        document.documentElement.dataset.mdbTheme = savedTheme;
    } else {
        themeStitcher.checked = isSystemThemeSetToDark;
        document.documentElement.dataset.mdbTheme = isSystemThemeSetToDark ? "dark" : "light";
    }

    // Add listener to theme toggler
    themeStitcher.addEventListener("change", (e) => {
        toggleTheme(e.target.checked);
    });

    const toggleTheme = (isChecked) => {
        const theme = isChecked ? "dark" : "light";
        document.documentElement.dataset.mdbTheme = theme;

        // Save preference to localStorage
        localStorage.setItem("theme", theme);
    };

    // Add listener to toggle theme with Shift + D
    document.addEventListener("keydown", (e) => {
        const tagName = e.target.tagName.toLowerCase();
        const type = (e.target.type || "").toLowerCase();

        if (tagName === "input" || tagName === "textarea" || type === "text") {
            return;
        }

        if (e.shiftKey && e.key === "D") {
            themeStitcher.checked = !themeStitcher.checked;
            toggleTheme(themeStitcher.checked);
        }
    });

</script>
<div class="container pb-3">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <!-- Back Button -->
            <a href="javascript:history.back()" class="btn btn-link">
                <i class="ti ti-arrow-left"></i> Back
            </a>

            <!-- Hidden you button for spacing -->
            <a href="/" class="btn btn-link" style="visibility: hidden">
                <i class="ti ti-user"></i> YOU
            </a>

        </div>

        <!-- Logo and Title -->
        <div class="text-center">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Logo_rijksoverheid.svg" alt="Logo" width="50"
                 class="mb-1">
            <h1 class="4 mb-0">DocGov</h1>
        </div>

        <div>
            <!-- Home Button -->
            <a href="/personality" class="btn btn-link">
                <i class="ti ti-user"></i> YOU
            </a>

            <!-- Home Button -->
            <a href="/" class="btn btn-link">
                <i class="ti ti-home"></i> Home
            </a>

        </div>
    </div>
    @isset($search)
        <p class="text-muted">
            Explore official reports, policies, and legislative documents to understand government decisions and
            initiatives. Search by topic, region, or timeframe to see how public issues are addressed, track the impact
            of
            policies, and access data that promotes transparency and accountability in governance.
        </p>

        <form id="searchForm" method="get" onsubmit="setFormAction()">
            <div class="d-flex justify-content-center my-4">
                <div class="input-group w-75">
                    <input
                        type="text"
                        class="form-control rounded"
                        placeholder="Search..."
                        name="query"
                        value="{{ request('query') }}"
                    />
                    <span class="input-group-text border-0" id="search-addon">
                <i class="ti ti-search"></i>
            </span>
                </div>
            </div>
        </form>
    @endisset
    @isset($buttons)
        <!-- Quick Summary/In-Depth Switch -->
        <div class="d-flex justify-content-center mb-4">
            <div class="btn-group" role="group">
                <!-- Quick Summary -->
                <input
                    type="radio"
                    class="btn-check"
                    name="summarySwitch"
                    id="quickSummary"
                    autocomplete="off"
                    {{ request()->is('summary') ? 'checked' : '' }}
                />
                <label class="btn btn-secondary" for="quickSummary">Quick Summary</label>

                <!-- In Depth -->
                <input
                    type="radio"
                    class="btn-check"
                    name="summarySwitch"
                    id="inDepth"
                    autocomplete="off"
                    {{ request()->is('search') ? 'checked' : '' }}
                    {{ !request()->is('summary') && !request()->is('search') ? 'checked' : '' }}
                />
                <label class="btn btn-secondary" for="inDepth">In Depth</label>
            </div>
        </div>
    @endisset
</div>

<!-- Search Bar -->
@yield("content")


<!-- MDB5 JS -->

<script
    type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.1.0/mdb.umd.min.js"
></script>
<script>
    function setFormAction() {
        // Grab the form
        const form = document.getElementById('searchForm');
        // Check which radio is selected
        if (document.getElementById('inDepth').checked) {
            form.action = '/search';
        } else {
            form.action = '/summary';
        }
        // Let the form submit
        return true;
    }
    document.addEventListener('DOMContentLoaded', function () {
        const quickSummaryRadio = document.getElementById('quickSummary');
        const inDepthRadio = document.getElementById('inDepth');
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.querySelector('input[name="query"]');

        // Helper function to check if search query is non-empty
        function queryIsNotEmpty() {
            return searchInput.value.trim() !== '';
        }

        // Listen for changes on the Quick Summary radio
        quickSummaryRadio.addEventListener('change', function () {
            if (queryIsNotEmpty()) {
                setFormAction();   // sets the appropriate form action
                searchForm.submit();
            }
        });

        // Listen for changes on the In-Depth radio
        inDepthRadio.addEventListener('change', function () {
            if (queryIsNotEmpty()) {
                setFormAction();   // sets the appropriate form action
                searchForm.submit();
            }
        });
    });
</script>
</body>
</html>
