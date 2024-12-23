<?php echo $__env->make('Auth.Layout.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('Auth.Layout.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<body class="overflow-x-hidden">

<?php echo $__env->make('Auth.Layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('Alert.Toast.warning', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('Alert.Toast.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('header-content'); ?>

<main class="mt-4 px-5 ">
    <?php echo $__env->yieldContent('content'); ?>
</main>
<?php echo $__env->make('Auth.Layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('script-tag'); ?>

</body>
</html>
<?php /**PATH H:\arz\resources\Merikharz/Auth/Layout/master.blade.php ENDPATH**/ ?>