<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
  echo "<div class='alert alert-danger'>Unauthorized access.</div>";
  exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.*, a.email FROM users u JOIN auth a ON u.id = a.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
  echo "<div class='alert alert-danger'>User not found.</div>";
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<div class="container mt-4">
  <h4>My Profile</h4>

  <div id="ajaxAlert"></div>

  <form id="updateProfileForm">
    <input type="hidden" name="user_id" value="<?= $user_id ?>">
    <!-- Name fields -->
    <div class="form-row">
      <div class="form-group col-md-4"><label>First Name</label>
        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
      </div>
      <div class="form-group col-md-4"><label>Middle Name</label>
        <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($user['middle_name']) ?>">
      </div>
      <div class="form-group col-md-4"><label>Last Name</label>
        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
      </div>
    </div>
    <!-- Address -->
    <div class="form-group"><label>Address</label>
      <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>">
    </div>
    <!-- Birthday, Age, Sex, Civil Status -->
    <div class="form-row">
      <div class="form-group col-md-3"><label>Birthday</label>
        <input type="date" name="birthday" class="form-control" value="<?= $user['birthday'] ?>">
      </div>
      <div class="form-group col-md-2"><label>Age</label>
        <input type="number" name="age" class="form-control" value="<?= $user['age'] ?>">
      </div>
      <div class="form-group col-md-3"><label>Sex</label>
        <select name="sex" class="form-control">
          <option value="">Select</option>
          <option value="Male" <?= $user['sex']=='Male'?'selected':'' ?>>Male</option>
          <option value="Female" <?= $user['sex']=='Female'?'selected':'' ?>>Female</option>
        </select>
      </div>
      <div class="form-group col-md-4"><label>Civil Status</label>
        <select name="civil_status" class="form-control">
          <option value="">Select</option>
          <option value="Single" <?= $user['civil_status']=='Single'?'selected':'' ?>>Single</option>
          <option value="Married" <?= $user['civil_status']=='Married'?'selected':'' ?>>Married</option>
        </select>
      </div>
    </div>
    <!-- Email -->
    <div class="form-group"><label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <button type="button" class="btn btn-warning ml-2" data-toggle="modal" data-target="#changePasswordModal">
      Change Password
    </button>
  </form>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog">
 <div class="modal-dialog" role="document">
  <form id="changePasswordForm" class="modal-content">
   <div class="modal-header">
     <h5 class="modal-title">Change Password</h5>
     <button type="button" class="close" data-dismiss="modal">&times;</button>
   </div>
   <div class="modal-body">
     <input type="hidden" name="user_id" value="<?= $user_id ?>">
     <div class="form-group"><label>Current Password</label>
       <input type="password" name="current_password" class="form-control" required>
     </div>
     <div class="form-group"><label>New Password</label>
       <input type="password" name="new_password" class="form-control" required>
     </div>
     <div class="form-group"><label>Confirm New Password</label>
       <input type="password" name="confirm_password" class="form-control" required>
     </div>
   </div>
   <div class="modal-footer">
     <button type="submit" class="btn btn-success">Change Password</button>
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
   </div>
  </form>
 </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  // AJAX profile update
  $("#updateProfileForm").submit(function(e){
    e.preventDefault();
    $.post('update_profile.php', $(this).serialize(), function(json){
      $('#ajaxAlert').html(`<div class="alert alert-${json.status=='success'?'success':'danger'}">${json.message}</div>`);
    }, 'json');
  });

  // AJAX password change
  $("#changePasswordForm").submit(function(e){
    e.preventDefault();
    $.post('change_password.php', $(this).serialize(), function(json){
      $('#changePasswordModal').modal('hide');
      $('#ajaxAlert').html(`<div class="alert alert-${json.status=='success'?'success':'danger'}">${json.message}</div>`);
    }, 'json');
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>