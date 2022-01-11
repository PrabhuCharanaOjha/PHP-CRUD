<?php
include("./dbconnection.php");

// upload data to database
if (isset($_POST['upload'])) {
  if (($_REQUEST['Name'] == "") || ($_REQUEST['Email'] == "") || ($_REQUEST['Mobile'] == "") || ($_FILES['uploadfile']['name'] == "")) {
    $msg = '<div class="alert alert-warning" roll="alert">plz fill all fields</div>';
  } else {
    $Name = $_REQUEST['Name'];
    $Email = $_REQUEST['Email'];
    $Mobile = $_REQUEST['Mobile'];
    $filename = $_FILES['uploadfile']['name'];
    $randomnumber = rand(56485, 156485);
    $fileuniquename = $randomnumber . $filename;
    $tempname = $_FILES['uploadfile']['tmp_name'];
    $folder = "image/" . $fileuniquename;

    $sql = "INSERT INTO datas (name,email,mobile,image) VALUES ('$Name','$Email','$Mobile','$fileuniquename')";

    if ($conn->query($sql)) {
      if (move_uploaded_file($tempname, $folder)) {
        $msg = '<div class="alert alert-success mt-3 shadow" roll=""alert>Data Inserted Successfully</div>';
      } else {
        echo "image upload failed";
        $msg = '<div class="alert alert-danger mt-3 shadow" roll="alert">Update faild</div>';
      }
    }
  }
}


// edit button***************************************
if (isset($_REQUEST['Edit'])) {
  $hId = $_REQUEST['hId'];
  $sql = "SELECT * FROM datas WHERE id='$hId'";
  $result = $conn->query($sql);
  if ($result->num_rows == 1) {
    $editdata = $result->fetch_assoc();
  }
}

// update exist data**********************************
if (isset($_REQUEST['update'])) {
  if (($_REQUEST['Name'] == "") || ($_REQUEST['Email'] == "") || ($_REQUEST['Mobile'] == "") || ($_FILES["uploadfile"]["name"] == "")) {
    $msg = '<div class="alert alert-warning shadow" roll="alert">plz fill all fields</div>';
  } else {
    $hId = $_REQUEST['hId'];
    $Name = $_REQUEST['Name'];
    $Email = $_REQUEST['Email'];
    $Mobile = $_REQUEST['Mobile'];
    $filename = $_FILES['uploadfile']['name'];
    $randomnumber = rand(56485, 156485);
    $fileuniquename = $randomnumber . $filename;
    $tempname = $_FILES['uploadfile']['tmp_name'];
    $folder = "image/" . $fileuniquename;

    // delete data from database
    $sql1 = "SELECT * FROM datas WHERE id='$hId'";
    $result = $conn->query($sql1);
    if ($row1 = $result->fetch_assoc()) {
      unlink("./image/" . $row1['image']);
    }

    // update data from database
    $sql = "UPDATE datas SET name='$Name',email='$Email',mobile='$Mobile' ,image='$fileuniquename' WHERE id='$hId'";

    if ($result = $conn->query($sql)) {
      if (move_uploaded_file($tempname, $folder)) {
        $msg = '<div class="alert alert-success mt-3 shadow" roll=""alert>Request Upadate Successfully</div>';
      } else {
        $msg = '<div class="alert alert-danger mt-3 shadow" roll="alert">Update faild</div>';;
      }
    } else {
      echo "query problem";
    }
  }
}


// delete data from database
if (isset($_REQUEST['Delete'])) {
  $hId = $_REQUEST['hId'];
  $sql1 = "SELECT * FROM datas WHERE id='$hId'";
  $result = $conn->query($sql1);
  if ($row1 = $result->fetch_assoc()) {
    unlink("./image/" . $row1['image']);
    $sql2 = "DELETE FROM datas WHERE id='$hId'";
    if ($conn->query($sql2)) {
      echo 'done';
    } else {
      echo "failed to delete";
    }
  } else {
    echo "failed delete file from folder";
  }
  echo '<meta http-equiv="refresh" content="0;url=?closed" />';
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--bootsrap css-->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!--font awesome css-->
  <link rel="stylesheet" href="css/all.min.css">

  <title>CRUD WITH PHP & SQL</title>
</head>

<body>
  <!-- Navbar start -->
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <a class="navbar-brand" href="#">CRUD</a>
  </nav>
  <!-- Navbar End -->

  <div class="container">
    <div class="row">
      <div class="col-sm-8 offset-2 border shadow p-5 my-2">
        <!-- Insert data start -->
        <h1 class="alert alert-warning shadow my-3 text-center">INSERT DATA INTO DATABASE</h1>
        <?php if (isset($msg)) {
          echo $msg;
        } ?>

        <form action="" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="form-group col">
              <label for="Name">Name</label>
              <input type="text" name="Name" class="form-control" placeholder="Please Enter Your Name" value="<?php if (isset($editdata['name'])) {
                                                                                                                echo $editdata['name'];
                                                                                                              } ?>">
            </div>
            <div class="form-group col">
              <label for="Email">Email</label>
              <input type="email" name="Email" class="form-control" placeholder="Please Enter Your Email" value="<?php if (isset($editdata['email'])) {
                                                                                                                    echo $editdata['email'];
                                                                                                                  } ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="Mobile Number">Mobile Number</label>
            <input type="number" name="Mobile" class="form-control" placeholder="Enter Your Mobile Number" value="<?php if (isset($editdata['mobile'])) {
                                                                                                                    echo $editdata['mobile'];
                                                                                                                  } ?>">
          </div>
          <div class="form-group">
            <label for="Uploadfile">Uploadfile</label>
            <input type="file" class="form-control-file" name="uploadfile" placeholder="Please Select Your Image">
          </div>
          <?php
          if (isset($_REQUEST['Edit'])) {
            echo '<input type="hidden" name="hId" value=' . $editdata["id"] . '><button type="submit" name="update" class="btn btn-primary  btn-block">UPDATE YOUR DATA</button>';
          } else {
            echo '<input type="Submit" name="upload" value="SUBMIT YOUR DATA" class="btn btn-success btn-block">';
          }
          ?>

        </form>
        <!-- Insert data End -->
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <!-- table start -->
          <?php
          $sql = "SELECT * FROM datas";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) { ?>
            <table class="table my-3 shadow text-center">
              <thead class="thead-dark">
                <tr>
                  <th>SLNO</th>
                  <th>NAME</th>
                  <th>EMAIL</th>
                  <th>MOBILE</th>
                  <th>IMAGE</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                ?>
                  <tr>
                    <td scope="row"><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['mobile']; ?></td>
                    <td><img src='./image/<?php echo $row['image'] ?>' alt="" height="50" width="50"></td>
                    <td>
                      <form action="" method="post">
                        <!-- Button update modal -->
                        <input type="hidden" name="hId" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-primary" name="Edit">
                          Update
                        </button>
                        <button type="submit" class="btn btn-danger" name="Delete">
                          delete
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } ?>
          <!-- table End -->
        </div>
      </div>
    </div>
  </div>

</body>
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/all.min.js"></script>

</html>