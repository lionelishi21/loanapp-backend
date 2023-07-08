@extends('reports.layouts.master')
@section('title', 'Account Statement')
@section('footer')
    @include('reports.layouts.footer', ['setting' => $setting])
@endsection
@section('header')
    @include('reports.layouts.header', ['setting' => $setting])
@endsection
@section('title-content')
    <table  width="100%">
        <tr>
            <td align="left" class="cell-title-medium">
               <strong>Account Statement</strong>
            </td>
            <td align="center" class="cell-title-large cell-text-left">
                Account Name:  {{ Illuminate\Support\Str::limit($pageData['account_display_name'], 20)}}
            </td>
            <td class="cell-title-medium cell-text-left">
                Account Number: {{$pageData['account_number']}}
            </td>
        </tr>
        <tr>
            <td align="left" class="cell-title-medium">
            </td>
            <td align="center" class="cell-title-large cell-text-left">
                    @if (isset($pageData['loan']['member']))
                        Member Name:  {{ Illuminate\Support\Str::limit($pageData['loan']['member']['first_name'].' '.$pageData['loan']['member']['last_name'], 20)}}
                    @elseif (isset($pageData['member']))
                    @endif
            </td>
            <td class="cell-title-medium cell-text-left">
                @if (isset($pageData['loan']['member']))
                    Member Phone:  {{ Illuminate\Support\Str::limit($pageData['loan']['member']['phone'], 12)}}
                @elseif (isset($pageData['member']))
                    Member Phone:  {{ Illuminate\Support\Str::limit($pageData['member']['phone'], 12)}}
                @endif
            </td>
        </tr>
    </table>
@endsection

@section('main-content')
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Date / Time</th>
                <th>Narration</th>
                <th class="cell-text-right">Debit</th>
                <th class="cell-text-right">Credit</th>
                <th class="cell-text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pageData['statement'] as $row)
            <tr>
                <td>{{$row['created_at']}}</td>
                <td>{{$row['narration']}}</td>
                <td class="cell-text-right">{{ $row['is_dr'] ? $row['display_amount'] : '-' }}</td>
                <td class="cell-text-right">{{ $row['is_cr'] ? $row['display_amount'] : '-' }}</td>
                <td class="cell-text-right">{{$row['balance']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection