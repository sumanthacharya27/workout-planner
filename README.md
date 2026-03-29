# GymPlanner Pro

GymPlanner Pro is a lightweight workout planning and tracking app built with **PHP + vanilla JavaScript** and a **Tailwind CSS UI**.

## Highlights

- Professional dashboard with key fitness metrics.
- Curated workout library with difficulty filters.
- Custom workout builder with exercise staging.
- Guided workout execution flow (progress bar + step controls).
- Workout history and progress insights (milestones + PR-style records).
- Local-first persistence via `localStorage`.

## Project Structure

- `index.php` – PHP entrypoint that serves `index.html`.
- `index.html` – Main application shell (Tailwind-based layout).
- `scripts/app.js` – Application logic and state management.
- `styles/main.css` – Minimal custom CSS layer on top of Tailwind.
- `php/` – Optional database API endpoints for future server persistence.

## Run locally

Use any PHP server from the repository root:

```bash
php -S localhost:8000
```

Then open: `http://localhost:8000`

## Notes

- Current app state is persisted locally in browser storage.
- Existing PHP API files are available for future backend integration.
