# SYMFONY test task

1. Install project:
```sh
$ git clone https://github.com/alexsobolenko/symfony-app.git your_project
$ cd your_project
```
2. Configure environment in `.env` (copy from `.env.example`)
3. Build compose: `docker compose up -d --build`
4. Go to php container: `docker compose exec -ti php bash`
5. Build database (run inside container) `./make.sh first_run`
