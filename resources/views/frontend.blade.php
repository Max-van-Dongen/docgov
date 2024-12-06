<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocGov</title>
    <!-- MDB5 CSS -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.1/mdb.min.css"
        rel="stylesheet"
    />
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>
<body style="background-color: #F1F1F1">
<!-- Header -->

<div class="container pb-3">
    <div class="d-flex align-items-center justify-content-between">
        <!-- Back Button -->
        <a href="javascript:history.back()" class="btn btn-link text-dark">
            <i class="ti ti-arrow-left"></i> Back
        </a>

        <!-- Logo and Title -->
        <div class="text-center">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Logo_rijksoverheid.svg" alt="Logo" width="50" class="mb-1">
            <h1 class="4 mb-0"><a href="/" style="color: #18181b">DocGov</a></h1>
        </div>

        <!-- Home Button -->
        <a href="/" class="btn btn-link text-dark">
            <i class="ti ti-home"></i> Home
        </a>
    </div>
    <p class="text-muted">
        Explore official reports, policies, and legislative documents to understand government decisions and
        initiatives. Search by topic, region, or timeframe to see how public issues are addressed, track the impact of
        policies, and access data that promotes transparency and accountability in governance.
    </p>

    <form action="/search" method="get">
        <div class="d-flex justify-content-center my-4">
            <div class="input-group w-75">
                <input type="text" class="form-control rounded" placeholder="Search..." name="query" value="{{request("query")}}">
                <span class="input-group-text border-0" id="search-addon">
        <i class="ti ti-search"></i>
        </span>
            </div>
        </div>
    </form>
</div>

<!-- Search Bar -->
@yield("content")


<!-- MDB5 JS -->
<script
    type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.1/mdb.min.js"
></script>
</body>
</html>
