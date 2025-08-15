<div class="row mb-4">
    <label for="profile_image" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Name</label>
    <div class="col-sm-9">
        <input class="form-control" type="text" id="name" name="name" value="{{@$data->name}}" placeholder="Enter Entity Full Name">
    </div>
</div>
<div class="row mb-4">
    <label for="profile_image" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Username</label>
    <div class="col-sm-9">
        <input class="form-control" type="text" id="username" name="username" value="{{@$data->username}}" placeholder="Enter Entity User Name" maxlength="50" pattern="[^ ]+" onkeypress="return event.charCode != 32">
    </div>
</div>
<div class="row mb-4">
    <label for="email" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Email</label>
    <div class="col-sm-9">
        <input type="email" class="form-control" id="email" name="email" value="{{@$data->email}}" placeholder="entity@example.com">
    </div>
</div>
