# Company Task Tracker System - FlowSync

A Laravel-based internal task management system designed for tracking projects, departments, teams, and individual tasks within a company. It allows users to create tasks, assign them to team members, set due dates and priorities, and create nested subtasks under any main task — enabling detailed breakdown of complex workflows.

---

## Project Contributors Information

### Jordan Kuak Kian Meng

- **Student ID:** BAI_A2009F-2605004
- **Email Address:** jordankuak-academic@gmail.com
- **Role:** Project Leader, Backend Developer, Database Designer

### Cheng Wei Le

- **Student ID:** 
- **Email Address:** cheng.academic123@gmail.com
- **Role:** Backend Developer, UIUX Designer

### Lim Swee Sheng

- **Student ID:** 
- **Email Address:** limsweesheng@gmail.com
- **Role:** Frontend Developer, Tester

### Andrew Tan Yan Rui

- **Student ID:** 
- **Email Address:** yanrui1216@gmail.com
- **Role:** Frontend Developer, Tester

---

## How to Installing the Project

01. Make sure you have the PHP, Composer, and Node.js installed on your system.

02. Clone the project repository to your local machine:
   ```bash
   git clone https://github.com/jordankuak-academic/flowsync.git
   ```

03. Navigate to the project directory:
   ```bash
   cd flowsync
   ```

04. Install the project dependencies:
   ```bash
   composer install
   npm install
   ```

05. Configure the environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

06. Set up the database connection in the `.env` file.

07. Run the database migrations:
   ```bash
   php artisan migrate
   ```

08. Run the database seeds:
   ```bash
   php artisan db:seed
   ```

09. Everytime run the command below to compile the frontend resources:
   ```bash
   npm run dev
   ```

10. Start the project:
   ```bash
   php artisan serve
   ```

---

## Develop the Project

01. Open the project in your preferred code editor.

02. When everytime start the project, pull the latest changes from the repository:
   ```bash
   git checkout develop
   git pull origin develop
   ```

03. Create a new branch for your feature:
   ```bash
   git checkout -b feature/[feature-name]
   ```

04. Submit your changes to the repository:
   ```bash
   git add .
   git commit -m "Add [feature-name]"
   git push origin feature/[feature-name]
   ```

05. Request a pull request to merge your feature branch into the `develop` branch.
