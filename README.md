# Blog.Backend

## psmever's Blog Back-End Source.

## Docker

```bash
* 빌드
# docker-compose build --force-rm

* 이미지 초기화
# docker system prune -a

# docker rmi $(docker images --filter "dangling=true" -q --no-trunc)
# docker rmi $(docker images -q --no-trunc)
# docker kill $(docker ps -q)
# docker rm $(docker ps -a -q)

# docker kill $(docker ps -q) && docker rm $(docker ps -a -q) && docker rmi $(docker images -q --no-trunc) && docker-compose build --force-rm

# docker-compose up -d

* 컨테이너 접속
# docker-compose exec nicepage-service /bin/bash

* production mysql
/dockerfiles/*.pem 추가.
# ssh -i /tmp/data/*.pem user@xxx.xxx.xxx.xxx -N -L xxxx:localhost:3306
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
