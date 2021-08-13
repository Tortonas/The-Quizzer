# The Quizzer!
<b>What is it?</b>
An automated quizzer where people can come, answer questions, collect points and compete! This is one of the first projects I've made when started learning Symfony.

![The Quizzer](readme.gif)

# Setup

1. Clone this repository
2. `$ docker-compose up`
3. `$ ./tools/build.sh`
4. Use admin:admin to login into the system

# Commands

1. `bin/console email:marketing-send` sends marketing emails to users who didn't get an email in the last 30 days

# What I used
1. Symfony 5
2. Bootstrap
3. Github actions for CI
4. PHPUNIT
