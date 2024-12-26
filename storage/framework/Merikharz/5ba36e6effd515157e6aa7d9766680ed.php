<?php $__env->startSection('content'); ?>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">

            <img src="<?php echo e(asset('merikhArz/src/images/perfectmone.svg')); ?>" alt="">
            <h1 class="text-sm font-bold">
                حواله پرفکت مانی
            </h1>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 ">
            <form action="" class="space-y-4">
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-28 leading-4">آدرس حساب مقصد :</p>
                    <input type="text" class="py-1.5 px-4 border border-black rounded-md text-black w-full">
                    <img src="<?php echo e(asset('merikhArz/src/images/pasteIcon.svg')); ?>" alt="">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-28 leading-4">مبلغ حواله (دلار) :</p>
                    <input type="text" class="py-1.5 px-4 border border-black rounded-md text-black w-full">
                    <img src="<?php echo e(asset('merikhArz/src/images/pasteIcon.svg')); ?>" alt="">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class=" font-bold text-mini-mini-base w-24 leading-4 text-sky-600 underline underline-offset-8">قوانین را می پذیرم :</p>
                    <input type="checkbox" class="py-1.5 px-4 border border-black rounded-md text-black ">

                </div>

                <div class="flex items-center rounded-lg space-x-2 space-x-reverse mt-4 ">
                    <img src="<?php echo e(asset('merikhArz/src/images/seke.svg')); ?>" alt="">
                    <h1 class="text-sm">
                        <span class="text-mini-base font-bold text-rose-700">0</span> ریال
                    </h1>
                </div>

                <div class="flex items-center   rounded-lg space-x-2 space-x-reverse">
                    <img src="<?php echo e(asset('merikhArz/src/images/seke.svg')); ?>" alt="">
                    <h1 class="text-sm">
                        موجودی فعلی شما <span class="text-mini-mini-base font-bold   <?php if($user->getCreaditBalance()>0): ?> text-green-600  <?php else: ?> text-rose-700 <?php endif; ?>"><?php echo e($user->getCreaditBalance()); ?></span> تومان میباشد
                    </h1>
                </div>

                <div class="flex items-center justify-center">
                    <button class="bg-gradient-to-b from-268832 to-80C714 flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-2.5 text-white rounded-lg w-full">
                        <span class="w-36 text-mini-base leading-4">پرداخت با درگاه بانک</span>
                        <img src="<?php echo e(asset('merikhArz/src/images/bankkart.svg')); ?>" alt="" class="bg-cover w-5 h-5 ">
                    </button>
                </div>

                <div class="flex items-center justify-center">
                    <button class="bg-gradient-to-b from-DE9408 to-FFC98B flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-2.5 text-white rounded-lg w-full">
                        <span class="w-36 text-mini-base leading-4">پرداخت از طریق کیف پول</span>
                        <img src="<?php echo e(asset('merikhArz/src/images/walletWhite.svg')); ?>" alt="" class="bg-cover w-5 h-5 ">
                    </button>
                </div>
            </form>


        </article>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('Panel.Layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH H:\arz\resources\Merikharz/Panel/Transmission/index.blade.php ENDPATH**/ ?>