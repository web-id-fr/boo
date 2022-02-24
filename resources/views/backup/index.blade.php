@extends('boo::layouts.app')

@section('content')
        <div class="content">
            <div class="title m-b-md">
                <div class="w-2/3 mx-auto">
                    <div class="bg-white shadow-md rounded my-6">
                        <table class="text-left w-full border-collapse">
                            <thead>
                            <tr>
                                <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Name</th>
                                <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Type</th>
                                <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Status</th>
                                <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($backups as $backup)
                                <tr class="hover:bg-grey-lighter">
                                    <td class="py-4 px-6 border-b border-grey-light">{{ $backup['name'] }}</td>
                                    <td class="py-4 px-6 border-b border-grey-light">{{ $backup['type'] }}</td>
                                    <td class="py-4 px-6 border-b border-grey-light">{{ $backup['status'] }}</td>
                                    <td class="py-4 px-6 border-b border-grey-light">{{ $backup['backup_at'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection
