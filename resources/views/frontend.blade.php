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
<body>
<!-- Header -->
<div class="container text-center">
    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Logo_rijksoverheid.svg" alt="Logo" width="50"
         class="mb-3">
    <h1 class="mb-3"><a href="/" style="color: #18181b">DocGov</a>
{{--        <span class="badge badge-primary">BETA</span>--}}
    </h1>
    <p class="text-muted">
        Explore official reports, policies, and legislative documents to understand government decisions and
        initiatives. Search by topic, region, or timeframe to see how public issues are addressed, track the impact of
        policies, and access data that promotes transparency and accountability in governance.
    </p>
</div>

<!-- Search Bar -->
<form action="/search" method="get">
<div class="d-flex justify-content-center my-4">
    <div class="input-group w-50">
        <input type="text" class="form-control rounded" placeholder="Search..." name="query" value="{{request("query")}}">
        <span class="input-group-text border-0" id="search-addon">
        <i class="ti ti-search"></i>
        </span>
    </div>
</div>
</form>
@yield("content")


<!-- MDB5 JS -->
<script
    type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.1/mdb.min.js"
></script>
</body>
</html>
