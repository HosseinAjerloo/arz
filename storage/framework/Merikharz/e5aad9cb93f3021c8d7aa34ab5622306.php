<?php $__env->startSection('header-content'); ?>
<section class="h-14 flex items-center justify-center px-4">
    <div class="flex items-center space-x-reverse space-x-2">
        <p class="text-base font-black ">
            ورود به حساب کاربری
        </p>
    </div>


</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<form class="flex flex-col justify-center items-center space-y-6 py-2 pb-6  border-b border-black/40">
    <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
        <img src="<?php echo e(asset('merikhArz/src/images/svgPhone.svg')); ?>" alt="" class="w-6 h-6">
        <input type="text"
               class="w-full h-full py-1.5 outline-none px-2 placeholder:text-center placeholder:text-sm"
               placeholder="شماره همراه  (*********09)">
    </div>
    <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
        <img src="<?php echo e(asset('merikhArz/src/images/key.svg')); ?>" alt="" class="w-6 h-6">
        <input type="password"
               class="w-full h-full py-1.5 outline-none px-2  placeholder:text-center  placeholder:text-sm"
               placeholder="کلمه عبور ">
    </div>

    <div class="flex items-center justify-between space-x-reverse space-x-2 w-full">
        <div class="flex items-center space-x-reverse space-x-2">
            <input type="checkbox" class="py-1.5 outline-none px-2 " placeholder="کلمه عبور ">
            <label class="text-mini-base font-bold">مرا به خاطر بسپار</label>
        </div>
        <div>
            <a href="" class="text-mini-base text-sky-600 underline underline-offset-8">فراموشی رمز عبور</a>
        </div>
    </div>
    <div class="flex items-center justify-between space-x-reverse space-x-1.5 w-full">
        <div class="flex items-center space-x-reverse space-x-2">
            <p class="text-mini-base">هنوز ثبت نام نکرده اید</p>
            <a class="text-mini-base text-sky-600 underline underline-offset-8" href="">ثبت نام</a>
        </div>
    </div>
    <button class="bg-gradient-to-b from-FFB01B to-DE9408 text-sm w-52 h-10 rounded-md text-white font-bold">ورود به
        حساب کاربری
    </button>

    <a class="text-sm w-52 h-10 rounded-md text-white font-bold bg-black flex items-center justify-center space-x-reverse space-x-2" >
        <img src="../../src/images/google.svg" alt="">
        <span>
             ورود سریع با گوگل
            </span>
    </a>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Auth.Layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH H:\merikhArz\resources\Merikharz/Auth/index.blade.php ENDPATH**/ ?>