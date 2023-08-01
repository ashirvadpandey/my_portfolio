
![1](https://github.com/akhilesh-patel/Ansible-GitHub-docker-AWS-EC2/assets/64592542/29eda64f-e9e0-4501-b813-edb3e7ff391f)


# Deploy Maven project with docker container on AWS EC2 Machine. PART-2

# Step 1: Create ubuntu machine on aws.
# Step 2: Install JAVA
        sudo apt upgrade
        apt update 
        sudo apt install default-jdk
        java -version
        wget -q -O - https://pkg.jenkins.io/debian/jenkins.io.key | sudo apt-key add - 
        sudo sh -c 'echo deb http://pkg.jenkins.io/debian-stable binary/ > /etc/apt/sources.list.d/jenkins.list'

* If you are getting error // verified because the public key is not available: NO_PUBKEY 5BA31D57EF5975CA sudo apt-get upgrade

* Solution :-

        sudo apt-get upgrade
        sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys <NO_PUBKEY >
        apt update 

# Step 3: Install jenkins 
        
        sudo apt install jenkins  
        sudo systemctl restart jenkins 
        sudo systemctl status jenkins 
        sudo systemctl  enable jenkins 

# Step 4: Enter public_ip:8080 into your browser 
        cat /var/lib/jenkins/secrets/initialAdminPassword  -  copy passwword -  install suggested plugins -   Create First Admin User
        

        	user - admin
	        password - pass@123
	        confirm password - pass@123
	        full name - Akhilesh Patel
	        E-email address - akhilpaatel2121@gmail.com    //Save and Continue 
	        http://52.91.101.1:8080/
  

# Step 5: Create job:  
Enter an item name - Freestyle project - ok - Github project - project url - https://github.com/akhilesh-patel/Ansible-GitHub-docker-AWS-EC2 - Source Code Management -https://github.com/akhilesh-patel/Ansible-GitHub-docker-AWS-EC2.git - Build Steps - Invoke top-level Maven targets - Maven Version - deafult - Goals - test install - Apply & Save -  Build the job

*  if you are geeting any error Build step 'Invoke top-level Maven targets' marked build as failure  Finished: FAILURE.

Slution : - Please install Maven

            apt install maven 

* Again build the jobI I hope build is succeesful

* How to find your war file after successful build your job -  Workspace of fisrt-job on Built-In Node

        cd  /var/lib/jenkins/workspace/fisrt-job/webapp/target

# Step 6: Step 4: How to deploy your  war file on  another  ubuntu  machine.
* Create new ubuntu machine.
* set password for ubuntu user

        vim /etc/ssh/sshd_config     // add this line
		
		PasswordAuthentication yes
		PermitRootLogin        yes    
 Save & exit
                
		systemctl restart sshd
		passwd ubuntu //  login as a root 

                 
 # Testing 
 
	ssh ubuntu@44.211.132.208
        Enter your password 

# Step 7:    Go to jenkins and install plugin
             Plugin Name - Publish Over SSH
             After install plugin Set user/password
             Configuaration global setting
             you will getting  Publish over SSH - SSH Servers - add
             
             Name - your ec2 machine name 
	        Hostname - machine public  ip 
	        Username  - ubuntu 
	        remote directory - .  //dot
        
        * Advanced  - Please click the  mark - Use password authentication, or use a different key - Chnage password // Enter your password 


## Test Configuration - Apply &  Save 

###  Go to your job configuration -  Post-build Actions -  Send build artifacts over SSH 
                source file - webapp/target/webapp.war
                Remove prefix -  webapp/target
                Remote directory -  .
*  Apply and  Save - build your job 

# Step 8: Install docker on target machine.
              curl -fsSL https://get.docker.com -o get-docker.sh
              sudo sh get-docker.sh
              docker --version
              systemctl start docker 
              systemctl enable docker
              usermod -aG docker ubuntu
              systemctl restart docker
              systemctl enable docker

# Step 9:  Create  docker file
vim Dokcerfile

        FROM tomcat:latest
        COPY ./webapp.war /usr/local/tomcat/webapps/
        RUN cp -r /usr/local/tomcat/webapps.dist/* /usr/local/tomcat/webapps/
 
## Create image from docker file.

        docker  build -t custome-img .
        docker images
## Create docker container from dcoker file 

        docker run -dit -p 8080:8080 --name custome-container custome-img

### http://44.202.90.75:8080/webapp/
 

        docker stop custome-container
        docker rm  custome-container
        docker rmi custome-img 

### Edit the jenkins job configuration 
        exec command  -   

        docker  build -t custome-img .
        docker run -dit -p 8080:8080 --name custome-container custome-img

### Edit index.jsp file - Again build your job.

# Step 10:  Install Docker Compose 
        
        sudo apt update
        sudo curl -L https://github.com/docker/compose/releases/download/v2.16.0/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
        sudo chmod +x /usr/local/bin/docker-compose
        docker compose version

# Step 11: create docker-compose file.
        version: '3'
        services:
           tomcat:
                build:
                        context: .
                        dockerfile: Dockerfile
                image: tomcat-img
                container_name: tomcat-con
                restart: always
	        ports: 
                        - '8080:8080'


## Run Docker compose command



		docker-compose up --build -d
		docker-compose down
		docker-compose up -d



# Step 12: Go to job configuration and edit :
        docker-comnpose down
        docker-compose up --build -d 
* edit the index.jsp file  and again build  your job
	

# Step 13: Install Ansible.
        sudo apt update
        sudo apt install ansible
        ansible --version
	
        apt-get install sshpass
        pip install --ignore-installed requests

        vim ansible. cfg
        
        [defaults]
        inventory= /home/ubuntu/hosts
        host_key_checking = False
        private_key_file = /home/ubuntu/dotnet.pem
        interpreter_python_warn = False
        ansible_python_interpreter=/usr/bin/python3.9


# Step 14: Create hosts file.
        cd /home/ubuntu/
        vim hosts
        [machine1]
        host-machine  ansible_host=54.211.16.255 ansible_user=ec2-user ansible_ssh_private_key_file=/home/ubuntu/dotnet.pem
        ansible all -m ping 


# Step 15: Create ansible playbook.
        firstplaybook.yml
        - hosts: all
  tasks:

    - name: Stop container
      command: docker stop custome-container
      ignore_errors: True

    - name: Remove Docker Images
      command: docker rmi -f custome-tomcat
      ignore_errors: True

    - name: Remove docker conatiner
      command: docker rm -f custome-container
      ignore_errors: True


    - name: Create and Run Docker container
      command: docker build -t custome-tomcat .

    - name: Create and run container
      command: docker run -dit -p 8085:8080 --name custome-container custome-tomcat
	  

* Run command -: 
                ansible-playbook -i hosts firstplaybook.yml

* http://44.202.90.75:8085/webapp/




	      
## Authors

- [@Akhilesh Patel](https://www.github.com/akhilesh-patel)




## 🚀 About Me
I'm a Devops Engineer...



## 🔗 Links

[![linkedin](https://img.shields.io/badge/linkedin-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)]( https://www.linkedin.com/in/akhilesh-patel-8983aa1a5/)





## FAQ
* Please feel free to ask me any question you have. I'm here to help and provide information to the best of my abilities










