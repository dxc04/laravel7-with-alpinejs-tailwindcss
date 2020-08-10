@extends('layouts.app')

@section('content')
    <div class="flex flex-col justify-center min-h-screen py-12 bg-gray-50 sm:px-6 lg:px-9">
        <div class="absolute top-0 right-0 mt-4 mr-4">
            @if (Route::has('login'))
                <div class="space-x-4">
                    @auth
                        <a
                            href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150"
                        >
                            Log out
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>

        <div class="flex items-center justify-center" x-data="csvData()">
            <div class="flex flex-col justify-around">
                <form class="w-full max-w-lg">
                    <template x-if="active === 'parse-section'">
                    <span>
                    <div class="flex flex-wrap ml-30 justify-center">
                        <h1 class="font-sans font-thin mb-4 text-grey text-xl">Start a new email campaing...</h1>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6 ml-40"
                        x-transition:enter="transition duration-1000"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-out duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
         
                        <label class="cursor-pointer mt-6">
                            <span class="border border-dashed border-gray-300 relative mt-2 leading-normal px-4 py-3 text-gray text-sm rounded" >Upload CSV</span>
                            <input type="file" class="hidden" id="fileUpload" x-ref="file" @change="fileName = $refs.file.files[0].name"/>
                            <input class="text-green-500 font-bold py-2 px-4 border-b-4 border-blue-dark hover:border-blue rounded"
                            type="button" id="upload" value="Parse" @click="parse()" />
                            <p class="text-gray-600 text-xs italic" x-text="fileName" />
                        </label>

                    </div>
                    <div class="flex flex-wrap w-full">
                        <img src="{{url('/img/undraw_email_campaign_qa8y.svg')}}" alt="Email Campaing" style="opacity: 0.8"/>
                    </div>
                    </span>
                    </template>
                    <template x-if="active === 'generate-section'">
                    <span 
                        x-transition:enter="transition duration-2000"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                    >
                        <a class="my-3 block mb-12 font-thin" href="javascript:;" @click="active = 'parse-section'"><span class="text-teal-600 text-lg hover:text-cool-gray-600">Back to upload...</span></a>
                        <div class="mb-6">
                            <h2 class="font-sans font-thin text-grey text-xl">Header Columns</h2>           
                            <div class="flex flex-row pt-4">
                                <template x-for="(col, index) in header" :key="index">
                                    <span x-text="col" class="mr-2 text-xs inline-flex items-center font-bold leading-sm px-3 py-1 bg-green-200 text-green-400 border-2 border-grey-300 rounded"></span>
                                </template>
                            </div>
                        </div>
                        
                        <template x-if="isShowColMenu">
                            <ul class="flex flex-col p-4 w-3/5">
                                <template x-for="(col, index) in header" :key="index">
                                    <li class="border-gray-400 flex flex-row">
                                        <div class="select-none cursor-pointer bg-white flex flex-1 items-center p-2 border-buttom border-gray-100  transition duration-500 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                                            <div class="text-gray-600 text-xs" x-text="col"></div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </template>

                        <h2 class="font-sans font-thin mb-4 text-grey text-xl">Templates</h2>   
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                                Subject
                            </label>
                            <input x-model="subject" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="subject" type="text">
                            <p class="text-gray-600 text-xs italic">To interpolate, insert column name by wrapping it with curly braces -- @{{colName}}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                                Message
                            </label>
                            <textarea x-model="message" class=" no-resize appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 h-48 resize-none" id="message"></textarea>
                            <p class="text-gray-600 text-xs italic">To interpolate, insert column name by wrapping it with curly braces -- @{{colName}}</p>
                            </div>
                        </div>
                        <div class="md:flex md:items-center">
                            <div class="md:w-1/3">
                            <button class="shadow bg-teal-400 hover:bg-teal-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="button" @click="generate">
                                Generate
                            </button>
                            </div>
                            <div class="md:w-2/3"></div>
                        </div>
                    </span>
                    </template>
                </form>
            </div>
            <template x-if="active == 'table-section'">
                <span>                     
                    <div class="px-12 sm:px-8 py-12 overflow-x-auto">
                    <a class="my-3 block mb-4 font-thin" href="javascript:;" @click="active = 'parse-section'"><span class="text-teal-600 text-lg hover:text-cool-gray-600">Back to upload...</span></a>
                    <a class="my-3 block mb-12 font-thin" href="javascript:;" @click="active = 'generate-section'"><span class="text-teal-600 text-lg hover:text-cool-gray-600">Back to generate...</span></a>   
                        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <template x-for="(col, index) in header" :key="index">
                                        <th x-text="col"
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        </th>
                                        </template>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Subject
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Message
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, ri) in dataList" :key="ri">
                                    <tr>
                                        <template x-for="id in Object.keys(row)">
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <div x-text="row[id]" class="flex items-center"></div>
                                        </td>
                                        </template>
                                    </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </span>
            </template>
        </div>

    </div>
@endsection
