@if(Auth::user()->type == 'admin')
<div class="row mb-4">
    <label for="profile_image" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Entity</label>
    <div class="col-sm-9">

        <select name="entity" id="entity" class="form-control">
            <option disabled selected>Select Entity</option>
            @if(count($data['entities']) > 0)
                @foreach($data['entities'] as $entity)
                    <option value="{{$entity->id}}" {{ @$data['site']['entity_id'] == $entity->id ? 'selected' : '' }} >{{$entity->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
@endif
<div class="row mb-4">
    <label for="profile_image" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Site Name</label>
    <div class="col-sm-9">
        <input class="form-control" type="text" id="site_name" name="site_name" value="{{@$data['site']['name']}}" placeholder="Enter Site Name">
    </div>
</div>
<div class="row mb-4">
    <label for="email" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Site Location</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="site_location" name="site_location" value="{{@$data['site']['location']}}" placeholder="Enter Site Location" >
    </div>
</div>
