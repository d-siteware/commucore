<x-guest-layout :title="__('impressum.title')">
    <h1 class="text-5xl">{{ __('impressum.title') }}</h1>

    <dl class="divide-y divide-zinc-50 dark:divide-zinc-600">
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm/6 font-medium ">{{__('impressum.register_name')}}</dt>
            <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ setting('organization.name') }}</dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm/6 font-medium ">{{__('impressum.register_id')}}</dt>
            <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ setting('organization.register_id') }}</dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm/6 font-medium ">{{__('impressum.register_at')}}</dt>
            <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ setting('organization.registered_date') }}</dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm/6 font-medium ">{{__('impressum.register_place')}}</dt>
            <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ setting('organization.court') }}</dd>
        </div>

        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm/6 font-medium ">{{__('impressum.represented_by')}}</dt>
            <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">
                <p>{{ \App\Models\Membership\Member::organizationRepresentativeString() }}</p>

            </dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm/6 font-medium ">  V.i.S.d § 18 Abs. 2 MStV</dt>
            <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">
                <ul>
                    <li>{{ \App\Models\Membership\Member::organizationRepresentativeString() }}</li>
                    <li>{{ setting('organization.email') }}</li>
                </ul>
            </dd>
        </div>

    </dl>
</x-guest-layout>
