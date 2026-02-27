@extends('layouts.layout-ds')

@section('content')
    <div>
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--text-primary)">
                    {{ __('ds.pages.forms.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('ds.pages.forms.subtitle') }}
                </div>
            </div>
            <div class="flex gap-2">
                <x-ds::button variant="secondary" icon="solar:restart-outline">Reset</x-ds::button>
                <x-ds::button icon="solar:diskette-linear">Save Changes</x-ds::button>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Basic Inputs -->
            <x-ds::card :title="__('ds.pages.forms.sections.inputs')"
                :description="__('ds.pages.forms.sections.inputs_desc')">
                <div class="mt-4 space-y-4">
                    <x-ds::input label="Email Address" placeholder="john@example.com" icon="solar:letter-linear"
                        helper="We'll never share your email." />

                    <x-ds::input type="password" label="Password" placeholder="••••••••" icon="solar:lock-password-linear"
                        rightIcon="solar:eye-closed-linear" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-ds::input label="First Name" placeholder="John" />
                        <x-ds::input label="Last Name" placeholder="Doe" />
                    </div>
                </div>
            </x-ds::card>

            <!-- States & Sizes -->
            <x-ds::card :title="__('ds.pages.forms.sections.states')"
                :description="__('ds.pages.forms.sections.states_desc')">
                <div class="mt-4 space-y-4">
                    <x-ds::input label="Error State" value="Invalid Value" error="This field is required."
                        icon="solar:danger-circle-linear" />

                    <x-ds::input label="Disabled Input" value="Cannot edit this" disabled
                        icon="solar:forbidden-circle-linear" />

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end">
                        <x-ds::input size="sm" placeholder="Small Input" />
                        <x-ds::input size="md" placeholder="Default Input" />
                        <x-ds::input size="lg" placeholder="Large Input" />
                    </div>
                </div>
            </x-ds::card>

            <!-- Textarea & Upload -->
            <x-ds::card :title="__('ds.pages.forms.sections.text_upload')"
                :description="__('ds.pages.forms.sections.text_upload_desc')">
                <div class="mt-4 space-y-4">
                    <x-ds::textarea label="Bio" placeholder="Tell us a little about yourself..." rows="4"
                        helper="275 characters remaining" />

                    <x-ds::file-upload label="Profile Picture" />
                </div>
            </x-ds::card>

            <!-- Selects & Toggles -->
            <x-ds::card :title="__('ds.pages.forms.sections.selects')"
                :description="__('ds.pages.forms.sections.selects_desc')">
                <div class="mt-4 space-y-6">
                    <x-ds::select label="Role" :options="['Admin', 'Editor', 'Viewer']" icon="solar:user-id-linear" />

                    <div class="flex flex-col gap-4">
                        <label class="text-sm font-medium text-(--text-primary)">Notifications</label>
                        <x-ds::toggle label="Enable email notifications" checked />
                        <x-ds::toggle label="Enable push notifications" />
                        <x-ds::toggle label="Disabled toggle" disabled />
                    </div>
                </div>
            </x-ds::card>

            <!-- Checkboxes & Radios -->
            <x-ds::card class="lg:col-span-2" :title="__('ds.pages.forms.sections.checks')"
                :description="__('ds.pages.forms.sections.checks_desc')">
                <div class="mt-4 grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Checkboxes -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-(--text-primary)">Checkboxes</h4>
                        <x-ds::checkbox label="I agree to the Terms and Conditions" checked />
                        <x-ds::checkbox label="Subscribe to newsletter" />
                        <x-ds::checkbox label="Disabled Checked" checked disabled />
                        <x-ds::checkbox label="Error Checkbox" error="You must agree to continue." />
                    </div>

                    <!-- Radios -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-(--text-primary)">Radio Buttons</h4>
                        <div class="flex flex-col gap-3">
                            <x-ds::radio name="plan" label="Free Plan (Ads)" value="free" checked />
                            <x-ds::radio name="plan" label="Pro Plan ($10/mo)" value="pro" />
                            <x-ds::radio name="plan" label="Enterprise (Custom)" value="ent" disabled />
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>

        <!-- Documentation -->
        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--text-secondary)">
                {{ __('ds.pages.forms.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.forms.docs.usage_title')"
                :description="__('ds.pages.tables.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6">
                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">
                            {{ __('ds.pages.forms.docs.example_code_title') }}</div>
                        <div
                            class="mt-3 overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-hover)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
                                <!-- Input -->
                                <x-ds::input label="Email" icon="solar:letter-linear" error="{{ $message }}" />

                                <!-- Select -->
                                <x-ds::select label="Country" :options="$countries" />

                                <!-- Checkbox -->
                                <x-ds::checkbox label="Remember me" />

                                <!-- Toggle -->
                                <x-ds::toggle label="Dark Mode" />
                            @endverbatim</code></pre>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection