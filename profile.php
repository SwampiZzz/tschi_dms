<div class="container my-4">
  <div class="row justify-content-center">
    <div class="col-md-6 text-center">
      <label for="pic-file">
        <img src="<?= $image_path ?>" alt="Profile Picture" id="img-display" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover; cursor: pointer;">
      </label>
      <input type="file" id="pic-file" onchange="return showPic()" style="display: none;">
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <input class="form-control mb-2" id="first-name" type="text" value="<?= $firstname ?>" placeholder="First Name">
      <input class="form-control mb-2" id="middle-name" type="text" value="<?= $middlename ?>" placeholder="Middle Name">
      <input class="form-control mb-2" id="last-name" type="text" value="<?= $lastname ?>" placeholder="Last Name">
      <select class="form-select mb-2" id="dropdown-department">
        <option value="">--Department--</option>
        <?= $deptopts ?>
      </select>
      <input id="save-profile-button" class="btn btn-primary w-100" type="submit" value="Update Profile" onclick="return updateProfileBtn()">
    </div>
  </div>
</div>
