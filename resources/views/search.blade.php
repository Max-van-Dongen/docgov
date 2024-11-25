<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocGov</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Montserrat, sans-serif;
        }
        .docgov-header {
            /*margin-top: 50px;*/
            text-align: center;
        }
        .docgov-header img {
            margin-bottom: 20px;
        }
        .docgov-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .docgov-header p {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 40px;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
        }
        .search-bar input {
            border-radius: 50px;
            padding: 10px 20px;
            width: 50%;
        }
        .search-bar button {
            border-radius: 50px;
            margin-left: -40px;
            z-index: 1;
        }
        .topics {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .topic-card {
            text-align: center;
            width: 150px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background-color: white;
            transition: transform 0.3s;
        }
        .topic-card:hover {
            transform: scale(1.05);
        }
        .topic-card img {
            width: 50px;
            margin-bottom: 10px;
        }
        .toggle-switch {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -20px;
        }
    </style>
</head>
<body>

<div class="container docgov-header">
    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Logo_rijksoverheid.svg" alt="Logo" width="50">
    <h1>DocGov</h1>
    <p>Explore official reports, policies, and legislative documents to understand government decisions and initiatives. Search by topic, region, or timeframe to see how public issues are addressed, track the impact of policies, and access data that promotes transparency and accountability in governance.</p>
</div>

<div class="container search-bar">
    <input type="text" class="form-control" placeholder="Search..">
    <button class="btn btn-primary"><i class="bi bi-search"></i></button>
</div>

<div class="toggle-switch">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="quickSummarySwitch">
        <label class="form-check-label" for="quickSummarySwitch">Quick Summary</label>
    </div>
</div>

<div class="container topics">
    <div class="topic-card">
        <i class="ti ti-home-2"></i>
        <p>Housing & Properties</p>
    </div>
    <div class="topic-card">
        <img src="https://via.placeholder.com/50" alt="Health">
        <p>Health & Social Services</p>
    </div>
    <div class="topic-card">
        <img src="https://via.placeholder.com/50" alt="Jobs">
        <p>Jobs & Employment</p>
    </div>
    <div class="topic-card">
        <img src="https://via.placeholder.com/50" alt="Environment">
        <p>Environment & Energy</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
