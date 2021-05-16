
![C2 logo](https://github.com/RollingArray/C2-storyline/blob/main/images/landing.png?raw=true)

***[`C2`](http://c2.rollingarray.co.in/)*** is a platform designed to nullify any ***bias*** in terms of ***race***, ***age***, ***gender***, ***disability*** and ***culture*** when treating a workforce and provide ***measurable matrix*** for a best fit person to grab a ***new opportunities***

***[`C2`](http://c2.rollingarray.co.in/)*** has the power to generate meaningful ***credibility metric*** for each individual team member by analysing the ***feedbacks*** provided by the ***reviewers*** of the ***deliverables***

---

## Table of Contents
- [:large_blue_circle: Typical Inequality Problem in Workplace](#large_blue_circle-typical-inequality-problem-in-workplace)
- [:large_blue_circle: Various types of Bias](#large_blue_circle-various-types-of-bias)
- [:large_blue_circle: Case Study](#large_blue_circle-case-study)
- [:large_blue_circle: Solution Approach](#large_blue_circle-solution-approach)
  - [:small_orange_diamond: Design Foundation](#small_orange_diamond-design-foundation)
    - [:small_blue_diamond: Projects](#small_blue_diamond-projects)
    - [:small_blue_diamond: Goal](#small_blue_diamond-goal)
    - [:small_blue_diamond: Activity](#small_blue_diamond-activity)
    - [:small_blue_diamond: Measurement Criteria](#small_blue_diamond-measurement-criteria)
    - [:small_blue_diamond: Measurement Criteria Characteristics](#small_blue_diamond-measurement-criteria-characteristics)
    - [:small_blue_diamond: Reviewer](#small_blue_diamond-reviewer)
    - [:small_blue_diamond: Feedback classification & Calculating Performance %](#small_blue_diamond-feedback-classification--calculating-performance-)
    - [:small_blue_diamond: Calculating Weighted Performances %](#small_blue_diamond-calculating-weighted-performances-)
    - [:small_blue_diamond: Calculating Activity Performance](#small_blue_diamond-calculating-activity-performance)
    - [:small_blue_diamond: Calculating Credibility](#small_blue_diamond-calculating-credibility)
    - [:small_blue_diamond: Define Equal Opportunities](#small_blue_diamond-define-equal-opportunities)
- [:large_blue_circle: Solution](#large_blue_circle-solution)
  - [:small_orange_diamond: C2 - Bring Equality In Diverse Workforce](#small_orange_diamond-c2---bring-equality-in-diverse-workforce)
  - [:small_orange_diamond: How It Works](#small_orange_diamond-how-it-works)
    - [:small_blue_diamond: Authenticate yourself](#small_blue_diamond-authenticate-yourself)
    - [:small_blue_diamond: My Projects](#small_blue_diamond-my-projects)
    - [:small_blue_diamond: Members](#small_blue_diamond-members)
    - [:small_blue_diamond: Sprints](#small_blue_diamond-sprints)
    - [:small_blue_diamond: Goals](#small_blue_diamond-goals)
    - [:small_blue_diamond: Project Activities](#small_blue_diamond-project-activities)
    - [:small_blue_diamond: Activity Assignee Self Review](#small_blue_diamond-activity-assignee-self-review)
    - [:small_blue_diamond: Activity Review](#small_blue_diamond-activity-review)
    - [:small_blue_diamond: Credibility Board](#small_blue_diamond-credibility-board)
- [:large_blue_circle: Contributing](#large_blue_circle-contributing)
  - [:small_orange_diamond: Repository](#small_orange_diamond-repository)
  - [:small_orange_diamond: System Requirement](#small_orange_diamond-system-requirement)
  - [:small_orange_diamond: Setting Up the project locally](#small_orange_diamond-setting-up-the-project-locally)
  - [:small_orange_diamond: Reporting Bugs](#small_orange_diamond-reporting-bugs)
- [:large_blue_circle: Author](#large_blue_circle-author)
- [:large_blue_circle: Show your support](#large_blue_circle-show-your-support)
- [:large_blue_circle: License](#large_blue_circle-license)


---
# :large_blue_circle: Typical Inequality Problem in Workplace

In a typical workplace, work done by a resource is normally evaluated based on ***feedback*** from ***supervisors*** or ***customers*** respectively. Feedback providers give feedback depending on their assessment of the deliverable against each criteria and it is very ***subjective***

<img width="250" align="right" src="https://github.com/RollingArray/C2-storyline/blob/main/images/pain-point.png?raw=true" alt="demo"/>

Feedback criteria and weightages can vary depending on the category of task / deliverable.

If the feedbacks are analysed, there could be ***various bias*** found in the feedback provided by the same person in different instances. Hence, this can bring ***`In-Quality`*** in a ***`Diverse workforce`***

**[⬆ back to top](#table-of-contents)**

---

# :large_blue_circle: Various types of Bias

Study shows, there are various types of bias encountered while judging resources while providing feedback for a certain deliverable . A bias in judgement may happen as consciously or unconsciously

<img align="center" src="https://github.com/RollingArray/C2-storyline/blob/main/images/bias.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

---

# :large_blue_circle: Case Study

<img width="400" align="left" src="https://github.com/RollingArray/C2-storyline/blob/main/images/case-study.png?raw=true"/>

`Joe` is managing a finical project. He has a team of `4 members`

Over a period of `time`, `Joe` wants to do an `Equality analysis` of all his team members for a `new engagement`, where he is looking for a best resource from his new engagement

**[⬆ back to top](#table-of-contents)**

---

# :large_blue_circle: Solution Approach
<img width="300" align="right" src="https://github.com/RollingArray/C2-storyline/blob/main/images/approach.png?raw=true"/>

> * :one: Design the data structure which can pull feedback related data from the day to day task deliverables in an project
> 
> * :two: Design an Algorithm which can evaluate these feedbacks and create a tangible output in terms of a credibility score of an individual
> * :three: Use the credibility score as baseline to find a assignee for an up coming opportunity

**[⬆ back to top](#table-of-contents)**

---

## :small_orange_diamond: Design Foundation
### :small_blue_diamond: Projects

> Each project consist of number of ***Goals***, ***Sprints***, ***Assignees*** & ***Reviewers***

<img width="500" src="https://github.com/RollingArray/C2-storyline/blob/main/images/projects.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Goal

> Each ***Goal*** is associated to certain ***Activities***

<img width="800" src="https://github.com/RollingArray/C2-storyline/blob/main/images/goal.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Activity

> Each ***Activity*** has associated certain properties and more specificity has number of ***reviewers*** to judge how well the assignee has achieved the ***results***

<img width="600" src="https://github.com/RollingArray/C2-storyline/blob/main/images/activity.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Measurement Criteria

> Each ***Activity Measurement Criteria*** has ***Characteristics*** & ***Performance Statistics***

<img width="600" src="https://github.com/RollingArray/C2-storyline/blob/main/images/measurement-criteria.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Measurement Criteria Characteristics

> ***Measurement Criteria*** can have 2 distinct Characteristics. ***Higher The Better Result*** or ***Lower The Better Result***

<img width="600" src="https://github.com/RollingArray/C2-storyline/blob/main/images/measurement-criteria-characteristics.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Reviewer

> ***Reviewers*** judge any deliverables for a certain ***duration*** over a defined set of ***Measurement Characteristics***  and provide verified results

<img width="700" src="https://github.com/RollingArray/C2-storyline/blob/main/images/reviewer.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Feedback classification & Calculating Performance %

> Each ***Feedback*** gets classified based on pre-defined ***Activity Completion Indicator*** and ***Performance in %*** gets calculated

<img width="700" src="https://github.com/RollingArray/C2-storyline/blob/main/images/feedback-classification.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Calculating Weighted Performances %

> For a certain ***Assignee*** in a given duration, the activities are having different ***weights***, and total weight for that duration should not cross ***100%***

<img width="500" src="https://github.com/RollingArray/C2-storyline/blob/main/images/weighted-performance.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Calculating Activity Performance

> Based on number of ***Weighted Performances***, an ***Activity Performance*** gets calculated by taking ***Mathematical Mean*** of all performances

<img width="700" src="https://github.com/RollingArray/C2-storyline/blob/main/images/activity-performance.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Calculating Credibility

> Upon calculating activity performance for all the tasks assigned to an assignee, ***Credibility*** of the assignee can be derived in % or out of 5 by taking the ***Summation*** of Activity Performances divided by ***Activity Weight***.

<img width="800" src="https://github.com/RollingArray/C2-storyline/blob/main/images/credibility.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

### :small_blue_diamond: Define Equal Opportunities

> Upon finding resources for a given ***New opportunity***, ***[`C2`](http://c2.rollingarray.co.in/)*** lists resources with ***Credibility Score***, hence the opportunity may gets assigned to the ***Top Scorer***. This nullifies any ***Bias*** in decision making and brings equality in diverse work force

<img width="700" src="https://github.com/RollingArray/C2-storyline/blob/main/images/opportunity.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

# :large_blue_circle: Solution
## :small_orange_diamond: C2 - Bring Equality In Diverse Workforce

***[`C2`](http://c2.rollingarray.co.in/)*** is a platform designed to nullify any ***bias*** in terms of ***race***, ***age***, ***gender***, ***disability*** and ***culture*** when treating a workforce and provide ***measurable matrix*** for a best fit person to grab a ***new opportunities***

***[`C2`](http://c2.rollingarray.co.in/)*** has the power to generate meaningful ***credibility metric*** for each individual team member by analysing the ***feedbacks*** provided by the ***reviewers*** of the ***deliverables***

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/c2.png?raw=true"/>

**[⬆ back to top](#table-of-contents)**

## :small_orange_diamond: How It Works 

### :small_blue_diamond: Authenticate yourself

> * In your desktop or mobile navigate to [***https://c2.rollingarray.co.in***](https://c2.rollingarray.co.in/)
> * User a valid email to create a free account
> * Activate the account
> * Once activated, use the email to login

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/authenticate-yourself.png?raw=true"/>


### :small_blue_diamond: My Projects

> * Once sign in, you will land to My Projects
> * Create a new project or you have been invited by  any other to team to join
> * From more options, tap on View Project Details to get in to the project
> * From more options, tap on Edit Project Details to edit the project details

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/my-projects.png?raw=true"/>

### :small_blue_diamond: Members

> * From left menu, you can navigate to Project Members
> * As an project administrator, you can add New Members and Reviewers to the project
> * Tap on + icon to get in to member search, 
> * Search for a user
> * Add user as Assignee or Reviewer to the Project
> <img src="https://github.com/RollingArray/C2-storyline/blob/main/images/project-members.png?raw=true"/>


### :small_blue_diamond: Sprints

> * From left menu, you can navigate to Project Sprints
> * Sprints are typically timelines defines for a specific period in a year
> * Sprints are probably the most important aspect of 
> * C2 ecosystem, every activity is strongly tide up with time line and the performance has been measured based on time

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/project-sprints.png?raw=true"/>

### :small_blue_diamond: Goals

> * From left menu, you can navigate to Project Goal
> * No Activity can be created on C2 ecosystem without having a Goal.
> * All the criteria to measure the Activity is strongly mapped to the Goal
> * Tap on + icon to create a new Goal
> * Tap on more option on each Goal to edit the Goal details

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/project-goals.png?raw=true"/>


### :small_blue_diamond: Project Activities

> * From left menu, you can navigate to Project Activities
> * Choose Sprint, Assignee & Goal to see all the activities
> * Tap on + icon to create a new Activity by providing measurement criteria.
> * Measurement criteria is an important aspect of C2 . Hence the system enforces a lot of fair policies to consider while review

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/project-activities.png?raw=true"/>

### :small_blue_diamond: Activity Assignee Self Review

> * From left menu, you can navigate to My Activity
> * You can see all the activities assigns to you
> * Tap on the Activity to get in to Activity Review page
> * Scroll down to Assignee self review section and goes to details by taping on more icon
> * Add self review comments  and provide measurable data based on your
> * Based on Activity measurement scale, platform will show what is the achieved result in comparison to the measurement scale

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/activity-assignee-self-review.png?raw=true"/>


### :small_blue_diamond: Activity Review

> * From left menu, you can navigate to My Reviews
> * You can see all the Activities assigns to you for review
> * Tap on the activity to get in to Activity Review page
> * Scrolls down to Review Details section and update review comment by taping on more icon
> * Provides the verified results and comments

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/activity-review.png?raw=true"/>


### :small_blue_diamond: Credibility Board

> * From left menu, you can navigate to Credibility Board
> * C2 analysis each feedback and tunnel them through custom algorithm to generate a Credibility Index for each resource by nullifying any bias opinion in terms of of race, age, gender, disability and culture
> * You can look at the Credibility Board to see how the review comments effected your teams Credibility Index
> * This score can be fair indicator to the best performing resource and a strong contender for next opportunity

<img src="https://github.com/RollingArray/C2-storyline/blob/main/images/credibility-board.png?raw=true"/>


**[⬆ back to top](#table-of-contents)**

# :large_blue_circle: Contributing

🙏👍🎉 First off, thanks for taking the time to contribute! 🎉👍👏

When contributing to this repository, please first discuss the change you wish to make via issue, email, or any other method with the owners of this repository before making a change. Please note we have a [Check the contributing guide](https://github.com/RollingArray/C2-storyline/blob/main/CODE_OF_CONDUCT.md), please follow it in all your interactions with the project.

## :small_orange_diamond: Repository
| Area | Repo |
|---|---|
| Client app | [https://github.com/RollingArray/C2-client-app](https://github.com/RollingArray/C2-client-app) |
| Server Api | [https://github.com/RollingArray/C2-server-api](https://github.com/RollingArray/C2-server-api) |
| Database | [https://github.com/RollingArray/C2-database](https://github.com/RollingArray/C2-database) |

## :small_orange_diamond: System Requirement
| Entity | Version |
|---|---|
| Browser | Chrome or any other |
| Node.JS | 14 or above |
| PHP | 7.4 or above |
| MySQL | 5.7 or above |
| Web Server | Apache or Nginx |

## :small_orange_diamond: Setting Up the project locally

> ### :small_red_triangle: Database Setup
> > #### :arrow_forward: Clone Database schema Repo
> > ```
> > git clone https://github.com/RollingArray/C2-database
> > ```
> 
> > #### :arrow_forward: Create a MySQL database and a MySQL user
> > ```
> > mysql -u root -p
> > mysql> CREATE DATABASE IF NOT EXISTS `c2_dev` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
> > mysql> CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
> > mysql> GRANT ALL PRIVILEGES ON c2_dev.* TO 'username'@'localhost';
> > mysql> quit
> > ```
> 
> > #### :arrow_forward: Generate database `Schema`
> > ```
> > cd /C2-database
> > mysql -u username -p c2_dev < c2_dev_schema.sql
> > ```
> 
> > #### :arrow_forward: Add database `Triggers`
> > ```
> > cd /C2-database
> > mysql -u username -p c2_dev < c2_dev_trigger.sql
> > ```
>
> > #### :arrow_forward: Add database `Stored Procedures`
> > ```
> > cd /C2-database
> > mysql -u username -p c2_dev < c2_dev_stored_procedure.sql
> > ```
>
> > #### :arrow_forward: You should see ERD as below
> >
> > <img src="https://github.com/RollingArray/C2-storyline/blob/main/images/erd.png?raw=true"/>
>
> ### :small_red_triangle: Server Setup
> > #### :arrow_forward: Clone C2 Server Repo and place inside htdocs or www folder for you apache / nginx server 
> > ```
> > git clone https://github.com/RollingArray/C2-server-api
> > ```
>
> > #### :arrow_forward: Server configuration 
> > ```
> > cd /htdocs/C2-server-api/v1 
> > cp environment.example.php environment.php    
> > ```
> 
> > Open environment.php to any code editor and update hashKey, JWT, db and email parameters
> > ```php
> > $environment = [
> > 	'hashKey' => [
> > 		'SALT' => '', //key size 16,
> > 	],
> > 
> > 	'JWT' => [
> > 		'CLIENT_ID' => '', // client id
> > 		'SERVER_ID' => '', // server id www.xyz.com
> > 		'EXPIRE_IN_SECONDS' => '', // exiporation in seconds, 60480
> > 	],
> > 	
> > 	'db' => [
> > 		'host' => '', // host
> > 		'username' => '', // database username
> > 		'password' => '', // database password
> > 		'database' => '', // database name
> > 		'port' => '' // database post
> > 	],
> > 
> > 	'email' => [
> > 		'smtp_host_ip' => '', // smtp host ip
> > 		'port' => 587, // smtp port
> > 		'smtp_username' => '', // smtp_username
> > 		'smtp_password' => '', // smtp_password
> > 		'support_email' => '', // support_email
> > 		'pretty_email_name' => '' // pretty_email_name
> > 	],
> > ];   
> > ```
> > #### :arrow_forward: Test server setup
> > ```
> 
> > Navigate to http://localhost:<port>/C2-server-api/v1/user/test
> > If all goes fine, you should see the response as below
> > ```json
> > {
> >     "success":true,
> >     "message":"Server reachable"
> > }
> > ```
> > <img src="https://github.com/RollingArray/C2-storyline/blob/main/images/success.png?raw=true"/>
> 
> ### :small_red_triangle: Client App
> Follow below guidelines to setup C2 app on your Windows/Mac/Linux machine
> 

> > #### :arrow_forward: Environment Setup
> > To get started with C2, the only requirement is a [Node.js](https://ionicframework.com/docs/reference/glossary#node) & npm environment. You may choose any code editor
> > It is recommend selecting the LTS version of [Node.js](https://ionicframework.com/docs/reference/glossary#node) to ensure best compatibility.
> 

> > #### :arrow_forward: Install the Ionic CLI
> > C2 client app is design using [ionic](https://ionicframework.com). Go to [Ionic CLI](https://ionicframework.com/docs/intro/cli) and install for your OS
> 

> > #### :arrow_forward: Clone Repo
> > ```
> > git clone https://github.com/RollingArray/C2-client-app
> > cd C2-client-app/
> > ```
> 

> > #### :arrow_forward: Install Dependency
> > ```
> > cd C2-client-app/
> > npm install
> > ```
> 

> > #### :arrow_forward: Setup local api
> > Go to `C2-client-app/src/environments/environment.ts` and update api endpoint to your local api endpoint
> > ```ts
> > export const environment = {
> > 	production: false,
> > 	apiEndpoint: 'http://localhost:8888/C2-server-api/v1/'
> > };
> > ```

> > #### :arrow_forward: Build and Run App
> > ```
> > ionic serve --o
> > ```
> > #### :arrow_forward: You should see the app opened up in your browser
> >
> > <img src="https://github.com/RollingArray/C2-storyline/blob/main/images/app.png?raw=true"/>


**[⬆ back to top](#table-of-contents)**

## :small_orange_diamond: Reporting Bugs

Issues and feature requests are welcome.<br />
Feel free to check [issues page](https://github.com/RollingArray/C2-storyline/blob/main/BUG_REPORT.md) if you want to contribute.<br />

# :large_blue_circle: Author
**Ranjoy Sen**

- https://rollingarray.co.in
- LinkedIn: [@ranjoysen](Https://www.Linkedin.Com/in/ranjoysen)
- Twitter: [@ranjoy85](Https://twitter.Com/ranjoy85)
- Github: [@RollingArray](https://github.com/RollingArray)

# :large_blue_circle: Show your support

Please ⭐️ this repository if this project helped you!


# :large_blue_circle: License
support@rollingarray.co.in | C2 | © [rollingarray.co.in](http://rollingarray.co.in/).<br />
This project is [Apache](https://github.com/RollingArray/C2-client-app/blob/main/LICENSE) licensed.