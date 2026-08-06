<footer class="w-full py-6 mt-auto border-t border-slate-200/60 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md">
    <div class="w-full px-6 mx-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-mint text-brand-deep dark:bg-brand-deep dark:text-brand border border-brand-deep/5 dark:border-brand-deep">
                    <span class="w-1.5 h-1.5 mr-1.5 bg-brand rounded-full animate-pulse"></span>
                    {{ \App\Support\RegionContext::name() ?? 'System' }} Online
                </span>
                <span>&copy; {{ date('Y') }} <strong>Kue Pandan Asli</strong>. All rights reserved.</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                <span class="hover:text-brand-deep dark:hover:text-brand transition-colors">v1.0.0</span>
                <span>&bull;</span>
                <span class="hover:text-brand-deep dark:hover:text-brand transition-colors">Reseller & Delivery Admin Portal</span>
            </div>
        </div>
    </div>
</footer>
