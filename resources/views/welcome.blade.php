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
            <template x-if="isLoading">
                <div class="fixed top-0 left-0 z-50 w-screen h-screen flex items-center justify-center" style="background: rgba(0, 0, 0, 0.3);">
                    <div class="bg-white border py-2 px-5 rounded-lg flex items-center flex-col">
                        <div class="loader-dots block relative w-20 h-5 mt-2">
                        <div class="absolute top-0 mt-1 w-3 h-3 rounded-full bg-green-500"></div>
                        <div class="absolute top-0 mt-1 w-3 h-3 rounded-full bg-green-500"></div>
                        <div class="absolute top-0 mt-1 w-3 h-3 rounded-full bg-green-500"></div>
                        <div class="absolute top-0 mt-1 w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="text-gray-500 text-xs font-light mt-2 text-center">
                        Please wait...
                        </div>
                    </div>
                </div>
            </template>

            <div class="flex flex-col justify-around">
                <form class="w-full max-w-lg">
                    <template x-if="active === 'parse-section'">
                    <span>
                    <div class="flex flex-wrap justify-center">
                        <h1 class="font-sans font-thin mb-4 text-grey text-xl">Start a new email campaing...</h1>
                    </div>
                    <div class="flex flex-wrap -mx-3 ml-40"
                        x-transition:enter="transition duration-1000"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-out duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
         
                        <label class="cursor-pointer mt-2">
                            <span class="border border-dashed border-gray-300 relative mt-2 leading-normal px-4 py-3 text-gray text-sm rounded" >Upload CSV</span>
                            <input type="file" class="hidden" id="fileUpload" x-ref="file" @change="fileName = $refs.file.files[0].name"/>
                            <input class="text-green-500 font-bold py-2 px-4 border-b-4 border-blue-dark hover:border-blue rounded"
                            type="button" id="upload" value="Parse" @click="parse()" />
                            <p class="text-gray-600 text-xs italic" x-text="fileName" />
                        </label>

                    </div>
                    <div class="flex flex-wrap w-full" >
                        <div class="hero-image">
                            <div class="hero-text">

                            </div>
                        </div>
                    </div>
                    </span>
                    </template>
                    <template x-if="active === 'generate-section'">
                    <span 
                        x-transition:enter="transition duration-2000"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                    >
                        <a class="my-3 block mb-12 font-thin" href="javascript:;" @click="backToParse()"><span class="text-teal-600 text-lg hover:text-cool-gray-600">Back to upload...</span></a>
                        <div class="mb-6">
                            <h2 class="font-sans font-thin text-grey text-xl">Campaing Templates</h2>           
                            <div class="flex flex-row pt-4">
                                <div class="relative inline-flex w-full">
                                    <svg class="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 412 232"><path d="M206 171.144L42.678 7.822c-9.763-9.763-25.592-9.763-35.355 0-9.763 9.764-9.763 25.592 0 35.355l181 181c4.88 4.882 11.279 7.323 17.677 7.323s12.796-2.441 17.678-7.322l181-181c9.763-9.764 9.763-25.592 0-35.355-9.763-9.763-25.592-9.763-35.355 0L206 171.144z" fill="#648299" fill-rule="nonzero"/></svg>
                                    <select x-model="selectedTplKey" @change="updateTpl()" class="w-full border border-gray-300 rounded text-gray-600 h-10 pl-5 pr-10 bg-white hover:border-gray-400 focus:outline-none appearance-none">
                                        <option value="null" disabled>Select a campaing template...</option>
                                        <template x-for="(tpl, index) in templates" :key="index">
                                            <option :value="tpl.name" x-text="tpl.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <template x-if="selectedTplKey !== 'null'">
                                <span>
                                    <div class="flex flex-row inline-block mt-3 justify-evenly">
                                        <p class="text-gray-400 text-xs italic ">Map csv columns to template's variables to easily update the template.</p>
                                    </div>
                                    <template x-for="(colVar, index) in selectedTpl.requiredVars" :key="index">
                                        <div class="flex flex-row inline-block mt-3 justify-evenly">
                                            <div class="w-1/5"><span x-text="colVar.label" class="mr-2 text-sm inline-block border-dashed border-b"></span></div>
                                            <div class="relative inline-flex block">
                                                <svg class="w-2 h-2 absolute top-0 right-0 m-2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 412 232"><path d="M206 171.144L42.678 7.822c-9.763-9.763-25.592-9.763-35.355 0-9.763 9.764-9.763 25.592 0 35.355l181 181c4.88 4.882 11.279 7.323 17.677 7.323s12.796-2.441 17.678-7.322l181-181c9.763-9.764 9.763-25.592 0-35.355-9.763-9.763-25.592-9.763-35.355 0L206 171.144z" fill="#648299" fill-rule="nonzero"/></svg>
                                                <select x-model="selectedTpl.mappedVars[colVar.key]" class="text-xs border border-gray-300 rounded text-gray-600 h-6 pl-3 pr-6 bg-white hover:border-gray-400 focus:outline-none appearance-none">
                                                    <option value="null" disabled>Map to...</option>
                                                    <template x-for="(col, index) in header" :key="index">
                                                        <option :value="col" :selected="col == emailCol" x-text="col"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="flex flex-row inline-block mt-3 justify-evenly">
                                        <a class="text-green-500 font-bold py-1 px-3 bg-cool-gray-100 border-b-4 border-blue-dark hover:border-blue rounded" href="javascript:;" @click="mappingDone">Apply Mapping</a>
                                    </div>
                                </span>
                            </template>

                        </div>

                        
                        <template x-if="isShowColMenu">
                            <ul class="flex flex-col p-4 w-3/5">
                                <template x-for="(col, index) in header" :key="index">
                                    <li class="border-gray-400 flex flex-row">
                                        <div class="select-none cursor-pointer bg-white flex flex-1 items-center p-2 border-buttom border-gray-200  transition duration-500 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                                            <div class="text-gray-600 text-xs" x-text="col"></div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <div x-show="selectedTplKey !== 'null'" class="mb-6">
                            <span>
                            <h2 class="font-sans font-thin text-grey text-xl">Header Columns</h2>           
                            <div class="flex flex-row pt-4">
                                <template x-for="(col, index) in header" :key="index">
                                    <span x-text="col" class="mr-3 px-3 py-1 text-sm border-cool-gray-300 inline-block border border-dashed text-green-400"></span>
                                </template>
                            </div>
                            </span>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                                Subject
                            </label>
                            <textarea x-model="subject" :readonly="!isMappingDone" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="subject"></textarea>
                            <p class="text-gray-400 text-xs italic">Update the columns accordingly. More on advanced usage of templating at <a class="text-blue-500" href="https://handlebarsjs.com/guide/builtin-helpers.html#if">handlebars</a>.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                                Message
                            </label>
                            <textarea x-model="message" :readonly="!isMappingDone" class=" no-resize appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 h-48 resize-none" id="message"></textarea>
                            <p class="text-gray-400 text-xs italic">Update the columns accordingly. More on advanced usage of templating at <a class="text-blue-500" href="https://handlebarsjs.com/guide/builtin-helpers.html#if">handlebars</a>.</p>
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
                    <div class="px-12 sm:px-8 py-8 overflow-x-auto">
                        <a class="my-3 block mb-4 font-thin" href="javascript:;" @click="backToParse()"><span class="text-teal-600 text-lg hover:text-cool-gray-600">Back to upload...</span></a>
                        <a class="my-3 block mb-12 font-thin" href="javascript:;" @click="active = 'generate-section'"><span class="text-teal-600 text-lg hover:text-cool-gray-600">Back to generate...</span></a>   
                        <div class="float-right mb-3">
                            <button class="shadow bg-teal-400 hover:bg-teal-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="button" @click="save()">
                                Save
                            </button>
                            <button class="shadow bg-green-500  hover:bg-teal-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="button" @click="save()">
                                Save and email
                            </button>
                        </div>
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
            <template x-if="active == 'finished-section'">
                <div class="flex flex-col justify-center">
                    <h2 class="font-sans font-thin text-grey text-2xl text-center mb-4">Great! A new campaign is done!</h2>  
                    <div class="flex flex-wrap w-full" >
                        <div class="success-hero-image">
                            <div class="hero-text">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

    </div>
@endsection
