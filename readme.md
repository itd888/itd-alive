需要在根目录下.env 配置redis的连接

[REDIS_A]
HOST = 10.18.7.28
PASS = abc123
PORT = 6379
EXPIRE = 864000


checkAPI 与 各个要检测存活的项目都要安装此包  
各个项目做刷新存活setAlive   
checkApi做检测getAlive
需要在运维中心数据库配置cfg_alive表,考虑是否需要创立新的组别,还是说在原有的组别中增加新的检测键和说明 


增加新的检测存活条目流程:
1.先在cfg_alive增加组别或在原有组别中增加键
2.在项目内配置相同的键 调用Alive::setAlive($key)设置存活
3.运维中心遍历cfg_alive时即可根据不同的api_host 调用api接口将 group_id与group_keys作为post参数发送到api接口,然后api内使用itd-alive包获取Alive::getMultiAlive()获取多个键的值

