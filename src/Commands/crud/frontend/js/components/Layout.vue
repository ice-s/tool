<template>
    <div>
        <nav class="main-header navbar navbar-expand navbar-dark navbar-light navbar-blue border-bottom-0">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <!-- Control Sidebar -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
                        <i  class="fas fa-th-large"></i>
                    </a>
                </li>

            </ul>
        </nav>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
               <!-- <img src="/images/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                     style="opacity: .8">-->
                <span class="brand-text font-weight-light">AdminLTE</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
<!--                        <img src="/images/AdminLTELogo.png" class="img-circle elevation-2" alt="User Image">-->
                    </div>
                    <div class="info">
                        <a class="d-block" href="#"></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <router-link tag="a" to="/dashboard" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </router-link>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <div class="content-wrapper">
            <slot/>
        </div>
        <aside class="control-sidebar control-sidebar-light">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5></h5>
                <a class="d-block" href="javascript:{}" @click.prevent="clickLogout">Logout</a>
            </div>
        </aside>
        <footer class="main-footer bg-light">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                Anything you want
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2020 AdminLTE.io</strong>. All rights reserved.

        </footer>
    </div>
</template>

<script>

    export default {
        data: function () {
            return {
                'visible': false
            }
        },
        created() {
        },
        mounted() {
        },
        replace: false,
        components: {},
        methods: {
            clickLogout() {
                this.logout();
            }
        }
    }
</script>
