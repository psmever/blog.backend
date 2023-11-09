<div align="center">

<div style="text-align: center;"><h4>📚 STACKS</h4></div>


<div style="text-align: center;">

<img src="https://img.shields.io/badge/php-525CBF?style=for-the-badge&logo=php&logoColor=black" alt="...">
<img src="https://img.shields.io/badge/laravel-525CBF?style=for-the-badge&logo=laravel&logoColor=black" alt="...">
<img src="https://img.shields.io/badge/docker-DEE0FA?style=for-the-badge&logo=docker&logoColor=black" alt="...">
<img src="https://img.shields.io/badge/mysql-1572B6?style=for-the-badge&logo=mysql&logoColor=black" alt="...">
</div>

<br />
<hr />

<h1 style="text-align: center;">Nicepage.pe.kr Back-End</h1>
</div>

# psmever's Blog Back-End Source.

## development

```bash

* docker 초기화

# composer docker:prune


* docker build

# composer docker:build


* docker shell

# composer docker:shell


* docker kill

# composer docker:kill


* app start

# composer app:start
```

## Docker Etc Command

```bash
* 빌드
# docker-compose build --force-rm

* 이미지 초기화
# docker system prune -a

* 이미지 삭제
# docker rmi $(docker images --filter "dangling=true" -q --no-trunc)
# docker rmi $(docker images -q --no-trunc)
# docker kill $(docker ps -q)
# docker rm $(docker ps -a -q)

* 초기화
# docker kill $(docker ps -q) && docker rm $(docker ps -a -q) && docker rmi $(docker images -q --no-trunc) && docker-compose build --force-rm

* docker start ( daemon )
# docker-compose up -d

* docker start
# docker-compose up

* 컨테이너 접속
# docker-compose exec blog-backend /bin/bash

* production mysql
/dockerfiles/*.pem 추가.
# ssh -i /tmp/data/*.pem user@xxx.xxx.xxx.xxx -N -L xxxx:localhost:3306

* docker 명형어 in container
# docker exec blog-backend /bin/bash -c "cd /var/www && composer app-clear:dev"
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
