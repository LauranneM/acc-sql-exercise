<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <title>SQL exercise</title>
  </head>
  <body>

    <div class="col">
      <form class="" action="index.php" method="get">
        <h2>Register new user</h2>
        <input class="item" type="text" name="firstname" placeholder="First Name">
        <input class="item" type="text" name="lastname" placeholder="Last Name">
        <input class=fullwidth type="text" name="email" placeholder="E-Mail">
        <select class="item" name="gender">
            <option value="Female">Female</option>
            <option value="Male">Male</option>
        </select>
        <label class="small-item" for="birthdate">Birth Date</label>
        <input class="small-item date" type="date" name="birthdate">
        <input class="fullwidth" type="submit" name="new" value="Save">

        <h2>Filter users</h2>
        <select class="" name="filter">
          <option value="0" selected="selected">Choose a filter</option>
          <option value="1">Users named Palmer</option>
          <option value="2">Female users</option>
          <option value="3">State beginning with a N</option>
          <option value="4">All google emails</option>
          <option value="5">Country repartition</option>
          <option value="7">Gender repartition</option>
          <option value="8">Age</option>
        </select>
        <input type="submit" value="filter">
      </form>
    </div>
    <div class="col-d">





    <?php
    $servername = "localhost";
    $username = "lauranne";
    $password = "laurd123";
    $dbname = "myDBPDO";

    if (isset($_REQUEST['filter']) || isset($_REQUEST['new'])){
      try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $title = '<h2>All users</h2>';
        $table_header = '<table>
          <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Gender</th>
            <th>State Code</th>
            <th>Email</th>
            <th>Age</th>
          <tr>';
        $sql = "SELECT first_name, last_name, gender, state_code, email, DATEDIFF(NOW(), STR_TO_DATE(birth_date, '%d/%m/%Y')) DIV 365 as age FROM users;";
        if (isset ($_REQUEST['new'])) {
          if ($_REQUEST['firstname'] == '' || $_REQUEST['lastname'] == '') {
            echo '<p class="alert">Please enter first name and last name';
          }
          else {
            $sql= "INSERT INTO users (first_name, last_name, email, gender, birth_date) VALUES ('".$_REQUEST['firstname']."', '".$_REQUEST['lastname']."', '".$_REQUEST['email']."', '".$_REQUEST['gender']."', '".$_REQUEST['birthdate']."');";
            $result = $conn->exec($sql);
            $now = new DateTime("now");
            $birth = new DateTime($_REQUEST['birthdate']);
            $age = $birth->diff($now)->format('%y');
            echo '<h2>Following user was successfully added</h2>';
            echo $table_header;
            echo "<tr>
              <td>".$_REQUEST['firstname']."</td>
              <td>".$_REQUEST['lastname']."</td>
              <td>".$_REQUEST['gender']."</td>
              <td></td>
              <td>".$_REQUEST['email']."</td>
              <td>".$age."</td>
            </tr>";
          }
        }
        else if (isset ($_REQUEST['filter'])) {
          switch ($_REQUEST['filter']) {
            case '1':
              $title = '<h2>Users named Palmer</h2>';
              $sql = "SELECT first_name, last_name, gender, state_code, email, DATEDIFF(NOW(), STR_TO_DATE(birth_date, '%d/%m/%Y')) DIV 365 as age FROM users WHERE last_name='Palmer';";
              break;
            case '2':
              $title = '<h2>Female users</h2>';
              $sql = "SELECT first_name, last_name, gender, state_code, email, DATEDIFF(NOW(), STR_TO_DATE(birth_date, '%d/%m/%Y')) DIV 365 as age FROM users WHERE gender='Female';";
              break;
            case '3':
              $title = '<h2>Users from States beginning with "N"</h2>';
              $sql = "SELECT first_name, last_name, gender, state_code, email, DATEDIFF(NOW(), STR_TO_DATE(birth_date, '%d/%m/%Y')) DIV 365 as age FROM users WHERE state_code LIKE 'N%';";
              break;
            case '4':
              $title = '<h2>Users from States beginning with "N"</h2>';
              $sql = "SELECT first_name, last_name, gender, state_code, email, DATEDIFF(NOW(), STR_TO_DATE(birth_date, '%d/%m/%Y')) DIV 365 as age FROM users WHERE email LIKE '%google%';";
              break;
            case '5':
              $title = '<h2>Users from States beginning with "N"</h2>';
              $sql = "SELECT country_code, COUNT(*) as count FROM users GROUP BY country_code ORDER BY count DESC;";
              $table_header = '<table>
                <tr>
                  <th>Country</th>
                  <th>Number of Users</th>
                <tr>';
              break;
            case '7':
              $title = '<h2>Users from States beginning with "N"</h2>';
              $sql = "SELECT gender, COUNT(*) as count, AVG(DATEDIFF(NOW(), STR_TO_DATE(birth_date, '%d/%m/%Y')) DIV 365) as avg_age  FROM users GROUP BY gender;";
              $table_header = '<table>
                <tr>
                  <th>Gender</th>
                  <th>Number of Users</th>
                  <th>Age average</th>
                <tr>';
              break;
          }
          $result = $conn->query($sql);
          // var_dump($result->num_row);
          echo $title;
          echo $table_header;
          if ($_REQUEST['filter'] == '5') {
            foreach ($result as $row) {
              echo "<tr>
                <td>".$row["country_code"]."</td>
                <td>".$row["count"]."</td>
              </tr>";
            }
          }
          else if ($_REQUEST['filter'] == '7') {
            foreach ($result as $row) {
              echo "<tr>
                <td>".$row["gender"]."</td>
                <td>".$row["count"]."</td>
                <td>".$row["avg_age"]."</td>
              </tr>";
            }
          }
          else {
            foreach ($result as $row) {
              echo "<tr>
                <td>".$row["first_name"]."</td>
                <td>".$row["last_name"]."</td>
                <td>".$row["gender"]."</td>
                <td>".$row["state_code"]."</td>
                <td>".$row["email"]."</td>
                <td>".$row["age"]."</td>
              </tr>";
            }
          }
        }
        echo "</table></div>";

      }
      catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
      }
    }
    $conn = null;

    ?>
    </div>
  </body>
</html>
