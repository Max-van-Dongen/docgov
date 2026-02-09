# DocGov

DocGov is a Laravel web application that improves transparency and accessibility in governance by collecting public government documents and making them searchable, enriched, and comparable.

In addition to browsing documents, DocGov includes an **AI-powered ingestion pipeline** that can automatically scrape document listings, fetch PDFs, extract text, generate summaries and metadata, and store entities/keywords for better discovery.

## Features

- **Search & browse documents**: Find documents by title, summary, description, people, keywords, and metadata.
- **Automated scraping (Open Overheid)**: Scrape search results from `open.overheid.nl` and collect PDF links + document metadata.
- **PDF ingestion**: Download PDFs and extract full text for indexing and downstream processing.
- **AI enrichment (local LLM)**:
  - Generate a **summary**
  - Generate a **clean title**
  - Generate a **short description**
  - Extract **people/entities** mentioned in the document
  - Extract **keywords/topics** for discovery
- **Related documents (relevancy scoring)**: Compute similarity between documents based on title overlap and publication date proximity.

## How it works (high level)

- **Scrape**: `app/Http/Controllers/ScrapeController.php` scrapes `open.overheid.nl` search results (title, PDF URL, document type/category, and original date).
- **Ingest & enrich**: `app/Http/Controllers/FileController.php` downloads each PDF, extracts text, and calls the local LLM gateway (via `app/Services/OpenAIService.php`) to generate metadata (summary/title/description) and extract people + keywords before persisting everything.
- **Compute relevancy**: `app/Http/Controllers/RelevancyController.php` calculates a relevancy score per document pair using shared title words and time proximity, and stores the result for “related documents” experiences.

## Local AI / model routing

DocGov uses a **local AI service** that exposes an **OpenAI-compatible API** (e.g. `POST /v1/chat/completions`, `GET /v1/models`). In this codebase the client lives in `app/Services/OpenAIService.php` (historical name), but it targets a local base URL (default: `http://llm.prsonal.nl`).

### Routing between active models (model pool)

For streaming endpoints (personalized summaries and search-results summaries), DocGov can **route requests across multiple active models** using a database-backed “model pool”:

- **Model registry**: Each available model is a row in the `llm_models` table (see `App\Models\LLMModel`).
- **Lease/lock**: When a request starts, the service selects the first model that is not busy and locks it using a DB transaction + `lockForUpdate()`.
- **Busy marking**: It marks the model as busy by setting `generating_since = now()`.
- **Request routing**: The outgoing payload’s `model` is set to the selected row’s `name`, so the local gateway executes that specific model.
- **Release**: When the streaming call finishes (or errors), `generating_since` is cleared so the model becomes available again.
- **Stale lease safety**: A model is considered available again if `generating_since` is older than ~5 seconds (protects against crashed/aborted streams).

## Technology Stack

- **Laravel** (routing/controllers, services, views)
- **Eloquent ORM** (models and relationships)
- **HTTP client** (scraping + PDF fetching)
- **Local LLM gateway (OpenAI-compatible API)** (summaries + structured extraction)

## Notes

- The AI pipeline will skip PDFs that were already ingested (based on the stored PDF URL).
- Very large PDFs are truncated before sending to the AI model to stay within token limits.
