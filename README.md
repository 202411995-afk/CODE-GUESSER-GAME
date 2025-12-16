# CODE-GUESSER-GAME

# Project Overview
"Code Guesser is a challenging and engaging trivia-style game where players must correctly identify or complete snippets of code from various programming languages. The game features a unique mechanic where the player's 'life' (or time/health bar) slowly depletes over time, simulating corrosion. Incorrect answers result in additional point/life deductions, adding urgency and risk to the gameplay."


Game Objectives,"The primary objective is to enhance the player's coding knowledge in a fun, entertaining way. Players aim to achieve the highest possible score by correctly identifying code snippets before their life/time runs out."

# Win/Lose Conditions
Win/Lose Conditions,* Win Condition: There is no traditional 'win' state that ends the game (unless all pre-loaded questions are completed). Winning is defined by achieving a high score and surviving for the longest duration possible. The game may implement a leaderboard feature to track competitive success. * Lose Condition: The game ends immediately when the player's Life/Health Bar reaches zero (due to a combination of time-based corrosion and incorrect answers).

# Technology Stack
PHP, CCS, XAMPP, MYSQL, SQL

# Team Members and Contributions
Domingo, John Renzyl M. (FULLSTACK, ASSETS)
Casimiro, Michael Angelo (GIF, VISUALS)
Cuevillas, Kathryz
Espartero, Jasmine
Dungca, John Daniel

# How to Play

Start: 

The game begins when the player clicks the "Start" button.
Life Corrosion: Immediately upon starting, a life or health bar begins to slowly deplete (corrode) at a steady rate. This acts as a global timer for the current round.
Question Display: A code snippet (the "Code to Guess") is displayed along with several multiple-choice answers or input fields.

Scoring:
Correct Answer: The player earns a set amount of points (e.g., +10 points) and a small amount of time/life may be restored.
Incorrect Answer: The player loses a significant number of points (e.g., -10 points) and incurs a large deduction from their Life Bar, accelerating the loss condition.

Game End: 
The game ends when the Life Bar reaches zero. The player's final score is recorded and displayed on the High Score screen.

# Steps-Place the project folder in your web server document root (e.g. htdocs/(filename)
-Ensure leaderboard.json exists in the same directory as the main PHP file. If it's not there, the app will create it automatically.
-Open Xampp and then Start APACHE, MYSQL
-Open your browser then type "http://localhost/login.php"
-Play the game.



# How to Run the Program

You will need PHP, XAMPP, MYSQL and VSCode in your device.

You are required to use :
xampp, vscdoe, php, mysql workbench

Add These Extensions in VSCode.
Php Profiler
Php Server
Php Intelephense
Vetur

Disable MYSQL 80
In the Services application, go to the mysql80 row and double click. Change Startup Type to Manual.
-WIN+R, then type > services.msc, find the > MYSQL 80, and disable it>.

Add your PHP folder to your System Environment Variables on Windows.

- Locate Your PHP Path
Find the folder where you installed or extracted PHP. Copy the address bar.
Common examples: C:\php or C:\xampp\php

- Open Environment Variables
Press Windows Key + R on your keyboard.

Type sysdm.cpl and hit Enter.

Go to the Advanced tab and click the Environment Variables... button at the bottom.

- Edit the Path
In the "System variables" section (the bottom box), find the row named Path and select it.

Click Edit...
Click New on the right side.
Paste the path to your PHP folder (from Step 1).
Click OK on all open windows to save.

Install XAMPP and Start Apache and MYSQL

Then open the code using VSCode, and click on PHP Server: Serve Project and type to your brower localhost/"yourfoldername"/login.php.
# Source Code
- https://drive.google.com/drive/folders/15e-g8RWWp7rVSqVKBY6teMiQa2Yv8yAT?usp=sharing

# Video Demonstration Links
- John Renzyl M. Domingo [Watch Video](https://drive.google.com/drive/folders/1UOdBuNx2w0EJePjQnmJJ4a0kiLWCsSXr?usp=sharing)
- Michael Angelo Casimiro [Watch Video](https://drive.google.com/drive/folders/1J4nkfFnkofhmOE3DtbmOPZkkUPXafZll?usp=drive_link)
- John Daniel Dungca [Watch Video](https://drive.google.com/drive/folders/1EAeC0dYtriLJf5m3Fy2rNy4HS9gLTO0W)

# OOP Implementation
(**1. Encapsulation**)

Location: Refactoring test1.php (Game Logic) and database.php (Config) → classes/GameSession.php

Description:
   - The global session variables $_SESSION['health'] and $_SESSION['level'] currently used in test1.php would be converted to private properties (e.g., private $health;) within a GameSession class to prevent direct
   modification.
  - Public getter and setter methods (e.g., setHealth($value)) would include data validation to ensure health does not drop below 0 or exceed the $MAX_HEALTH defined in test1.php
  - Database credentials currently exposed as raw variables in database.php would be encapsulated within a Database class with private properties.

(**2. Inheritance**)

Location: Refactoring login.php (Auth) and test1.php (Player Actions) → classes/User.php (Parent) and classes/Player.php (Child)

Description:
   -A parent User class would contain the authentication logic (login/password verification) currently found in login.php.
   -A Player class would extend User, inheriting the login capabilities (username, user_id) while adding game-specific methods like save_game_result() and properties like current_score currently defined as standalone
   functions in test1.php.

(**3. Polymorphism**)

Location: Refactoring menu.php (Leaderboard Modes) → classes/GameMode.php Interface

Description:
   -The menu.php file currently handles single and multi game modes using a string identifier and conditional queries.
   -This would be refactored into a GameMode interface with a method getLeaderboard().
   -Two classes, SinglePlayerMode and MultiPlayerMode, would implement this interface. The getLeaderboard() method would be overridden in each class to execute the specific SQL query required for that mode (e.g.,
   filtering by WHERE game_mode = 'single').

(**4. Abstraction**)

Location: Refactoring test1.php (Task Logic) → classes/AbstractGame.php

Description:
   -The game loop in test1.php relies on specific tasks defined in the $TASK_LEVELS array.
   -An AbstractGame class would define abstract methods like initializeGame() and processTurn().

This forces any specific game type (e.g., a hypothetical "PHP Quiz" vs "Java Quiz") to implement these methods, abstracting the complexity of the session management and time-tracking logic seen in test1.php.
# Downloadable Links

https://www.mysql.com/downloads/
https://www.apachefriends.org/
https://code.visualstudio.com/
https://www.php.net/downloads.php (VS17 x64 Thread Safe)

You need to use download this GDRIVE files to be able to use the intended visuals in the game:
https://drive.google.com/drive/folders/1SXNZNGvA5vlID2p5BTCoeGqepK7hjUG2?usp=sharing



