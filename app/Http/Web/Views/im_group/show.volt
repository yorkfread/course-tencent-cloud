{% extends 'templates/main.volt' %}

{% block content %}

    {% set group.about = group.about ? group.about : '这个家伙真懒，什么都没有留下~' %}
    {% set apply_group_url = '' %}
    {% set users_url = url({'for':'web.im_group.users','id':group.id}) %}
    {% set active_users_url = url({'for':'web.im_group.active_users','id':group.id}) %}

    <div class="breadcrumb">
        <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a href="{{ url({'for':'web.im_group.list'}) }}">群组</a>
            <a><cite>{{ group.name }}</cite></a>
        </span>
    </div>

    <div class="layout-main clearfix">
        <div class="layout-content">
            <div class="layui-card">
                <div class="layui-card-header">小组介绍</div>
                <div class="layui-card-body group-about">{{ group.about }}</div>
            </div>
            <div class="layui-card">
                <div class="layui-card-header">小组成员</div>
                <div class="layui-card-body">
                    <div id="user-list" data-url="{{ users_url }}"></div>
                </div>
            </div>
            <br>
        </div>
        <div class="layout-sidebar">
            <div class="sidebar">
                {{ partial('im_group/show_owner') }}
            </div>
            <div class="sidebar wrap">
                <button class="layui-btn layui-btn-fluid apply-group" data-url="{{ apply_group_url }}">申请加入</button>
            </div>
            <div class="sidebar" id="active-user-list" data-url="{{ active_users_url }}"></div>
        </div>
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('web/js/im_group.show.js') }}

{% endblock %}