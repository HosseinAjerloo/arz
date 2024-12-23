<?php $__env->startSection('header-content'); ?>
    <section class="h-14 flex items-center justify-center px-4">
        <div class="flex items-center space-x-reverse space-x-2">
            <p class="text-base font-black ">
                فراموشی رمز عبور
            </p>
        </div>


    </section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <form class="flex flex-col justify-center items-center space-y-6 py-2 pb-6  border-b border-black/40" method="POST" action="<?php echo e(route('post.forgotPassword')); ?>">
        <?php echo csrf_field(); ?>
        <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
            <img src="<?php echo e(asset('merikhArz/src/images/svgPhone.svg')); ?>" alt="" class="w-6 h-6">
            <input type="text"
                   class="w-full h-full py-1.5 outline-none px-2 placeholder:text-center placeholder:text-sm"
                   placeholder="شماره همراه  (*********09)" name="mobile">
        </div>
        <button class="bg-gradient-to-b from-FFB01B to-DE9408 text-sm w-52 h-10 rounded-md text-white font-bold">
            ارسال لینک
        </button>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Auth.Layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH H:\arz\resources\Merikharz/Auth/forgot.blade.php ENDPATH**/ ?>