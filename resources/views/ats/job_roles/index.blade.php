@extends('layouts.ats')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">職種管理</h1>

        <a href="{{ route('ats.job_roles.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded">
            職種を追加
        </a>
    </div>

    @if($jobRoles->isEmpty())
        <div class="p-4 bg-gray-50 border rounded">
            <p class="text-gray-700 mb-2">
                まだ職種が登録されていません。
            </p>
            <p class="text-sm text-gray-500">
                少なくとも1つの職種を登録すると、求人を作成できます。
            </p>
        </div>
    @else
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-left">表示名</th>
                    <th class="border p-2 text-left">内部名</th>
                    <th class="border p-2 text-left">状態</th>
                    <th class="border p-2 text-right">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobRoles as $role)
                    <tr>
                        <td class="border p-2">
                            {{ $role->display_name }}
                        </td>
                        <td class="border p-2 text-sm text-gray-500">
                            {{ $role->internal_name }}
                        </td>
                        <td class="border p-2">
                            {{ $role->is_active ? '有効' : '無効' }}
                        </td>
                        <td class="border p-2 text-right">
                            <a href="{{ route('ats.job_roles.edit', $role) }}"
                               class="text-blue-600 text-sm">
                                編集
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
