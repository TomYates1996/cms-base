Use as a base for copying to create other projects that use my cms-core package.

Setup Instructions
1) Copy the file to your own folder - run the following in order;
   git clone https://github.com/tomyates1996/cms-base.git temp-folder
   mv temp-folder [CLIENT_NAME]
2) Remove old git
   cd [CLIENT_NAME]
   rm -rf .git or Remove-Item -Recurse -Force .git
3) Initialise a new git project
   git init
   git add .
   git commit -m "Initial Commit"
3.5) Create a repo on github then in cmd/powershell
   git remote add origin https://github.com/YOUR_GITHUB/REPO_NAME.git
   git branch -M main
4) Push your new project to github
   git push -u origin main
5) Open your project in code editor and run:
   cp .env.example .env
6) run: composer install
7) run: php artisan key:generate
8) Fill out the .env with the settings you want to use.
9) php artisan migrate
10) npm install
