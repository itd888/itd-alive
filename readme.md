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




