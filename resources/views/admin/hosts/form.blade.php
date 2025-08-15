@if(Auth::user()->type == 'admin')
    <div class="row mb-4">
        <label for="profile_image" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Entity</label>
        <div class="col-sm-9">

            <select name="entity" id="entity" class="form-control">
                <option disabled selected>Select Entity</option>
                @if(count($data['entities']) > 0)
                    @foreach($data['entities'] as $entity)
                        <option value="{{$entity->id}}" {{ @$data['host']['entity_id'] == $entity->id ? 'selected' : '' }} >{{$entity->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
@endif
<div class="row mb-4">
    <label for="profile_image" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Name</label>
    <div class="col-sm-9">
        <input class="form-control" type="text" id="name" name="name" value="{{@$data['host']['name']}}" placeholder="Enter Host's Full Name">
    </div>
</div>
<div class="row mb-4">
    <label for="email" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Email</label>
    <div class="col-sm-9">
        <input type="email" class="form-control" id="email" name="email" value="{{@$data['host']['email']}}" placeholder="host@example.com">
    </div>
</div>
