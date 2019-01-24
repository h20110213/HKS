<div class="col-md-3 u-margin-16 card">
    <div class="row">
        <a href="{{ route('department.okr', $department->id) }}" class="u-ml-8 u-mr-8">
            <img src="{{ $department->getAvatar() }}" alt="" class="avatar text-center organizationIcon bg-white">
        </a>
        <div class="u-ml-8 u-mr-8 align-self-center">
            <a href="{{ route('department.okr', $department->id) }}">
                <span class="mb-0 font-weight-bold text-black-50">{{ $department->name }}</span>
            </a>
            @if ($department->children->count()>0 && $show)
            <a href="{{ route('department.index', $department) }}"><i class="far fa-plus-square pl-2"></i></a>
            @endif
            <br>
            @if ($department->user_id == auth()->user()->id)
            <a href="{{ route('department.create', $department->id) }}" data-toggle="tooltip" data-placement="bottom"
                title="新增部門"><i class="fas fa-plus-circle u-margin-4"></i></a>
            <a href="{{ route('department.member.setting', $department->id) }}" data-toggle="tooltip" data-placement="bottom"
                title="新增成員"><i class="fas fa-user-plus u-margin-4"></i></a>
            <a href="{{ route('department.edit', $department->id) }}" data-toggle="tooltip" data-placement="bottom"
                title="編輯部門"><i class="fas fa-edit u-margin-4"></i></a>
            <a href="#" onclick="document.getElementById('departmentDelete{{ $department->id }}').submit()" data-toggle="tooltip"
                data-placement="bottom" title="刪除部門"><i class="fas fa-trash-alt"></i></a>
            <form method="POST" id="departmentDelete{{ $department->id }}" action="{{ route('department.destroy', $department->id) }}">
                @csrf
                {{ method_field('DELETE') }}
            </form>
            @endif
        </div>
    </div>
    @if ($department->okrs)
    @foreach ($department->okrs as $okrs)
    <div class="row justify-content-center mb-4">
        <div class="col-lg-3 text-center">
            <span class="font-weight-bold text-black-50" style="font-size:14px;">Objective</span>
        </div>
        <div class="col-lg-7 text-black-50 text-center text-lg-left">{{ $okrs['objective']->title }}</div>
    </div>
    @endforeach
    @else
    <div class="row justify-content-center mb-4">
        <div class="col-lg-3 text-center mw-100">
            <span class="font-weight-bold text-black-50" style="font-size: 14px;">Objective</span>
        </div>
        <div class="col-lg-7 text-black-50 text-center text-lg-left">尚未具有進行中的Objective</div>
    </div>
    @endif
</div>
